<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAmocrmContactRequest;
use App\Models\AmocrmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class AmocrmController extends Controller
{
    /**
     * Method used to periodically update access token using refresh token
     *
     * @param AmocrmToken $token
     * @return bool
     */
    static public function refreshToken(AmocrmToken $token)
    {
        // Refresh AmoCRM access token
        $response = Http::post(config('app.amocrm_url') . '/oauth2/access_token', [
            'client_id' => config('app.amocrm_id'),
            'client_secret' => config('app.amocrm_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->refresh_token,
            'redirect_uri' => config('app.url'),
        ]);

        // Update token in database
        $data = $response->json();
        if ($response->ok()) {
            $token->update([
                'expires_in' => $data['expires_in'],
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
            ]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Set
     *
     * @param CreateAmocrmContactRequest $request
     * @param $lead_id
     * @return JsonResponse
     */
    public function setContact(CreateAmocrmContactRequest $request, $lead_id): JsonResponse
    {
        // Create new contact
        $response = Http::withToken($request->access_token)
            ->post(config('app.amocrm_url') . '/api/v4/contacts', [
                [
                    'name' => $request->name,
                    'custom_fields_values' => [
                        [
                            'field_id' => 2515177, // phone id
                            'values' => [
                                [
                                    'value' => $request->phone,
                                    'enum_code' => 'WORK'
                                ]
                            ]
                        ],
                    ],
                ]
            ]);

        if ($response->failed()) {
            $log = LogController::addLog("Создание нового контакта и привязка его к лиду: {$lead_id}", 0);

            if (!$log) {
                return response()->json(['status' => 'error', 'detail' => 'Не удалось добавить лог в историю'], 500);
            }

            return response()->json($response->json(), 400);
        }

        $contact_id = $response->json()['_embedded']['contacts'][0]['id'];


        // Add comment to new contact
        $response = Http::withToken($request->access_token)
            ->post(config('app.amocrm_url') . '/api/v4/contacts/' . $contact_id . '/notes', [
                [
                    'note_type' => 'common',
                    'params' => [
                        'text' => $request->comment,
                    ]
                ]
            ]);

        if ($response->failed()) {
            $log = LogController::addLog("Создание контакта: {$contact_id} и привязка его к лиду: {$lead_id}", 0);

            if (!$log) {
                return response()->json(['status' => 'error', 'detail' => 'Не удалось добавить лог в историю'], 500);
            }

            return response()->json($response->json(), 400);
        }


        // Link new contact to new lead
        $response = Http::withToken($request->access_token)
            ->post(config('app.amocrm_url') . '/api/v4/leads/' . $lead_id . '/link', [
                [
                    'to_entity_id' => $contact_id,
                    'to_entity_type' => 'contacts',
                ]
            ]);

        if ($response->failed()) {
            $log = LogController::addLog("Создание контакта: {$contact_id} и привязка его к лиду: {$lead_id}", 0);

            if (!$log) {
                return response()->json(['status' => 'error', 'detail' => 'Не удалось добавить лог в историю'], 500);
            }

            return response()->json($response->json(), 400);
        }


        // Add success action to history
        $log = LogController::addLog("Создание контакта: {$contact_id} и привязка его к лиду: {$lead_id}", 1);

        if (!$log) {
            return response()->json(['status' => 'error', 'detail' => 'Не удалось добавить лог в историю'], 500);
        }

        return response()->json(['status' => 'ok'], 200);
    }


    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeads(Request $request)
    {
        // Get list of leads from AmoCRM API
        $response = Http::withToken($request->access_token)
            ->get(config('app.amocrm_url') . '/api/v4/leads', [
                'with' => 'contacts',
                'order' => [
                    'updated_at' => 'desc',
                ],
                'limit' => 25,
            ]);

        if ($response->failed()) {
            return response()->json($response->json(), 400);
        }

        // Filter unused data
        $data = $response->json();
        $leads = [];

        for ($i = 0; $i < count($data['_embedded']['leads']); $i++) {
            $lead = $data['_embedded']['leads'][$i];

            $leads[] = [
                'id' => $lead['id'],
                'name' => $lead['name'],
                'created_at' => $lead['created_at'],
                'haveContacts' => count($lead['_embedded']['contacts']) > 0,
            ];
        }

        return response()->json($leads, 200);
    }

    /**
     * Method should be used once to get access token
     *
     * @return string
     */
    public function getTokenFromCode(): string
    {
        // Get AmoCRM access token from code
        $response = Http::post(config('app.amocrm_url') . '/oauth2/access_token', [
            'client_id' => config('app.amocrm_id'),
            'client_secret' => config('app.amocrm_secret'),
            'grant_type' => 'authorization_code',
            'code' => config('app.amocrm_code'),
            'redirect_uri' => config('app.url'),
        ]);

        // Insert token in database
        $data = $response->json();
        if ($response->ok()) {
            $token = AmocrmToken::first();

            if (!$token) {
                AmocrmToken::create([
                    'expires_in' => $data['expires_in'],
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                ]);
            }

            return redirect('/');
        } else {
            return $data['detail'];
        }
    }
}
