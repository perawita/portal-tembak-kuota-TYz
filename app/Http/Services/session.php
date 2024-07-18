<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

class session
{
    /**
     * Constructor to delete specified session names.
     *
     * @param array $session_names Array of session names to delete
     * @return string|null Returns a message if a session cannot be deleted
     */
    public function __construct(array $session_names)
    {
        foreach ($session_names as $session_name) {
            if (session()->has($session_name)) {
                session()->forget($session_name);
            } else {
                return response()->json("Session " . $session_name . " can't be deleted", 404);
            }
        }
    }
}
