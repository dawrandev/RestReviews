<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\VerificationCode;
use App\Services\EskizSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    protected EskizSmsService $smsService;

    public function __construct(EskizSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    #[OA\Post(
        path: "/api/auth/send-code",
        summary: "Tasdiqlash kodini yuborish",
        description: "Telefon raqamiga SMS orqali 4 raqamli tasdiqlash kodini yuboradi. Kod 5 daqiqa amal qiladi.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["phone"],
                properties: [
                    new OA\Property(
                        property: "phone",
                        type: "string",
                        description: "Telefon raqami (998XXXXXXXXX formatida)",
                        example: "998901234567"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Tasdiqlash kodi muvaffaqiyatli yuborildi",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tasdiqlash kodi yuborildi"),
                        new OA\Property(property: "phone", type: "string", example: "998901234567"),
                        new OA\Property(
                            property: "code",
                            type: "string",
                            nullable: true,
                            description: "Test rejimida qaytadi, production da null",
                            example: "1234"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validatsiya xatosi",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Validation error"),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            example: ["phone" => ["The phone field is required."]]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "SMS yuborishda xatolik",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "SMS yuborishda xatolik yuz berdi. Qaytadan urinib ko'ring.")
                    ]
                )
            )
        ]
    )]
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:9|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $this->cleanPhone($request->phone);

        // Generate 4-digit code
        $code = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        // Save to database (expires in 5 minutes)
        VerificationCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_used' => false,
        ]);

        // Send SMS via Eskiz
        $smsSent = $this->smsService->sendVerificationCode($phone, $code);

        // For testing without Eskiz - log the code
        Log::info("Verification code for {$phone}: {$code}");

        if (!$smsSent && !config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => 'SMS yuborishda xatolik yuz berdi. Qaytadan urinib ko\'ring.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tasdiqlash kodi yuborildi',
            'phone' => $phone,
            // In test mode, return code in response since SMS won't contain it
            'code' => config('services.eskiz.test_mode', true) ? $code : null,
        ]);
    }

    #[OA\Post(
        path: "/api/auth/verify-code",
        summary: "Kodni tekshirish va login/register",
        description: "Tasdiqlash kodini tekshiradi. Agar foydalanuvchi yangi bo'lsa, ism va familiya bilan ro'yxatdan o'tkazadi. Mavjud foydalanuvchi uchun login qiladi.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["phone", "code"],
                properties: [
                    new OA\Property(
                        property: "phone",
                        type: "string",
                        description: "Telefon raqami",
                        example: "998901234567"
                    ),
                    new OA\Property(
                        property: "code",
                        type: "string",
                        description: "4 raqamli tasdiqlash kodi",
                        example: "1234"
                    ),
                    new OA\Property(
                        property: "first_name",
                        type: "string",
                        description: "Ism (yangi foydalanuvchilar uchun majburiy)",
                        example: "Abdulla",
                        nullable: true
                    ),
                    new OA\Property(
                        property: "last_name",
                        type: "string",
                        description: "Familiya (yangi foydalanuvchilar uchun majburiy)",
                        example: "Valiyev",
                        nullable: true
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Muvaffaqiyatli login yoki ro'yxatdan o'tish",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tizimga kirdingiz"),
                        new OA\Property(property: "is_new_user", type: "boolean", example: false),
                        new OA\Property(
                            property: "client",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "first_name", type: "string", example: "Abdulla"),
                                new OA\Property(property: "last_name", type: "string", example: "Valiyev"),
                                new OA\Property(property: "full_name", type: "string", example: "Abdulla Valiyev"),
                                new OA\Property(property: "phone", type: "string", example: "998901234567"),
                                new OA\Property(property: "image_path", type: "string", nullable: true, example: null)
                            ]
                        ),
                        new OA\Property(
                            property: "token",
                            type: "string",
                            description: "Bearer token keyingi so'rovlar uchun",
                            example: "1|abcdefghijklmnopqrstuvwxyz123456789"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Noto'g'ri tasdiqlash kodi",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Noto'g'ri tasdiqlash kodi")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Kod topilmadi yoki muddati tugagan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Tasdiqlash kodi topilmadi yoki muddati tugagan")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validatsiya xatosi yoki yangi foydalanuvchi uchun ism/familiya kerak",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ism va familiya kiritish shart"),
                        new OA\Property(property: "requires_registration", type: "boolean", example: true),
                        new OA\Property(property: "errors", type: "object", nullable: true)
                    ]
                )
            )
        ]
    )]
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:9|max:20',
            'code' => 'required|string|size:4',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $this->cleanPhone($request->phone);
        $code = $request->code;

        // Find active verification code
        $verification = VerificationCode::where('phone', $phone)
            ->active()
            ->latest()
            ->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Tasdiqlash kodi topilmadi yoki muddati tugagan',
            ], 404);
        }

        if (!$verification->isValid($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Noto\'g\'ri tasdiqlash kodi',
            ], 401);
        }

        $verification->markAsUsed();

        $client = Client::where('phone', $phone)->first();

        if (!$client) {
            if (!$request->filled('first_name') || !$request->filled('last_name')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ism va familiya kiritish shart',
                    'requires_registration' => true,
                ], 422);
            }

            $client = Client::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $phone,
            ]);

            $isNewUser = true;
        } else {
            $isNewUser = false;
        }

        // Generate API token (using Sanctum)
        $token = $client->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => $isNewUser ? 'Ro\'yxatdan o\'tdingiz' : 'Tizimga kirdingiz',
            'is_new_user' => $isNewUser,
            'client' => [
                'id' => $client->id,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'full_name' => $client->full_name,
                'phone' => $client->phone,
                'image_path' => $client->image_path,
            ],
            'token' => $token,
        ]);
    }

    #[OA\Get(
        path: "/api/auth/me",
        summary: "Foydalanuvchi profilini olish",
        description: "Autentifikatsiya qilingan foydalanuvchining profilini qaytaradi",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Profil ma'lumotlari",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "client",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "first_name", type: "string", example: "Abdulla"),
                                new OA\Property(property: "last_name", type: "string", example: "Valiyev"),
                                new OA\Property(property: "full_name", type: "string", example: "Abdulla Valiyev"),
                                new OA\Property(property: "phone", type: "string", example: "998901234567"),
                                new OA\Property(property: "image_path", type: "string", nullable: true, example: null)
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Autentifikatsiya xatosi - token noto'g'ri yoki yo'q",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function me(Request $request)
    {
        $client = $request->user();

        return response()->json([
            'success' => true,
            'client' => [
                'id' => $client->id,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'full_name' => $client->full_name,
                'phone' => $client->phone,
                'image_path' => $client->image_path,
            ],
        ]);
    }

    #[OA\Post(
        path: "/api/auth/logout",
        summary: "Tizimdan chiqish",
        description: "Joriy tokenni bekor qiladi va foydalanuvchini tizimdan chiqaradi",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Muvaffaqiyatli logout",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tizimdan chiqdingiz")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Autentifikatsiya xatosi",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tizimdan chiqdingiz',
        ]);
    }

    /**
     * Clean phone number
     */
    protected function cleanPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure it starts with 998
        if (!str_starts_with($phone, '998')) {
            $phone = '998' . $phone;
        }

        return $phone;
    }
}
