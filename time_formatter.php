<?php

class TimeFormatter
{
    public static function formatTimestamp($timestamp) {
        // Convert timestamp to Unix timestamp if it's a string
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
    
        // If conversion fails, return an error message
        if ($timestamp === false) {
            return "Invalid date";
        }
    
        // Calculate time difference
        $timeDifference = time() - $timestamp;
    
        if ($timeDifference < 60) {
            return $timeDifference . ' seconds ago';
        } elseif ($timeDifference < 3600) {
            return floor($timeDifference / 60) . ' minutes ago';
        } elseif ($timeDifference < 86400) {
            return floor($timeDifference / 3600) . ' hours ago';
        } elseif ($timeDifference < 604800) {
            return floor($timeDifference / 86400) . ' days ago';
        } else {
            return date('M d, Y', $timestamp);
        }
    }
    
    
}
