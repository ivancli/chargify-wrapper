<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/26/2016
 * Time: 5:00 PM
 */

namespace Invigor\Chargify\Controllers;


use Invigor\Chargify\Models\Note;
use Invigor\Chargify\Traits\CacheFlusher;
use Invigor\Chargify\Traits\Curl;

class NoteController
{
    use Curl, CacheFlusher;

    public function create($subscription_id, $fields)
    {
        return $this->__create($subscription_id, $fields);
    }

    public function update($subscription_id, $note_id, $fields)
    {
        return $this->__update($subscription_id, $note_id, $fields);
    }

    public function get($subscription_id, $note_id)
    {
        return $this->__get($subscription_id, $note_id);
    }

    public function allBySubscription($subscription_id)
    {
        return $this->__allBySubscription($subscription_id);
    }

    private function __create($subscription_id, $fields)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/notes.json";
        $data = array(
            "note" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $note = $this->_post($url, $data);
        if (isset($note->note)) {
            $note = $this->__assign($note->note);
        }
        return $note;
    }

    private function __update($subscription_id, $note_id, $fields)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/notes/{$note_id}.json";
        $data = array(
            "note" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $note = $this->_put($url, $data);
        if (isset($note->note)) {
            $note = $this->__assign($note->note);
        }
        return $note;
    }

    private function __get($subscription_id, $note_id)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/notes/{$note_id}.json";
        $note = $this->_get($url);
        if (isset($note->note)) {
            $note = $note->note;
            $output = $this->__assign($note);
            return $output;
        } else {
            return $note;
        }
    }

    private function __allBySubscription($subscription_id)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/notes.json";
        $notes = $this->_get($url);
        if (is_array($notes)) {
            $notes = array_pluck($notes, 'note');
            $output = array();
            foreach ($notes as $note) {
                $output[] = $this->__assign($note);
            }
            return $output;
        } else {
            return $notes;
        }
    }

    private function __assign($input_note)
    {
        $note = new Note;
        foreach ($input_note as $key => $value) {
            if (property_exists($note, $key)) {
                $note->$key = $value;
            }
        }
        return $note;
    }

}