<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class EventController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL');
    }

    public function getToken(Request $request)
    {
        $clientId = $request->input('clientId');
        $clientSecret = $request->input('clientSecret');

        $client = new Client();
        $response = $client->post("{$this->baseUrl}/Auth", [
            'json' => [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
            ]
        ]);

        return response()->json(json_decode($response->getBody(), true));
    }

    public function createEvent(Request $request)
    {
        $token = $request->header('Authorization');
        $client = new Client();

        $response = $client->post("{$this->baseUrl}/Events", [
            'headers' => [
                'Authorization' => $token,
            ],
            'json' => [
                'id' => $request->input('id'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'startDate' => $request->input('startDate'),
                'endDate' => $request->input('endDate'),
            ]
        ]);

        return response()->json(json_decode($response->getBody(), true));
    }

    public function getAllEvents(Request $request)
    {
        $token = $request->header('Authorization');

        $top = $request->query('top');
        $skip = $request->query('skip');
        $filter = $request->query('filter');
        $orderBy = $request->query('orderBy', 'startDate');
        $sortDirection = $request->query('sortDirection', 'asc');

        $validOrderByFields = ['title', 'startDate', 'endDate', 'description', 'id'];
        if (!in_array($orderBy, $validOrderByFields)) {
            return response()->json(['error' => 'Invalid orderBy parameter'], 400);
        }

        $validSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $validSortDirections)) {
            return response()->json(['error' => 'Invalid sortDirection parameter'], 400);
        }

        // Prepare query parameters
        $queryParams = [
            '$top' => $top,
            '$skip' => $skip,
            '$orderby' => "{$orderBy} {$sortDirection}",
        ];

        if ($filter) {
            $queryParams['$filter'] = $filter;
        }

        $client = new Client();

        try {
            $response = $client->get("{$this->baseUrl}/Events", [
                'headers' => [
                    'Authorization' => $token,
                ],
                'query' => $queryParams,
            ]);

            return response()->json(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch events',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getEvent(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $client = new Client();

        $response = $client->get("{$this->baseUrl}/Events/{$id}", [
            'headers' => [
                'Authorization' => $token,
            ]
        ]);

        return response()->json(json_decode($response->getBody(), true));
    }

}
