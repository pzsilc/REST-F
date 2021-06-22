<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/HolidayKind.php';

class HolidayKindView extends View
{
    public function get()
    {
        $kinds = HolidayKind::all();
        foreach($kinds as $kind)
            $kind->id = $kind->id;

        return $this->json([
            'type' => 'success',
            'data' => $kinds
        ], 200);
    }
}

?>