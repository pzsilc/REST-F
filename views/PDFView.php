<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/HolidayEvent.php';
require_once __dir__.'/../traits/ExternalDatabase.php';
require_once __dir__.'/../traits/IPNavigator.php';
require_once __dir__.'/../mpdf60/mpdf.php';

class PDFView extends View
{
    use ExternalDatabase;
    use IPNavigator;

    public function post()
    {
        $holiday_event_id = $this->request->post('id');
        $e = HolidayEvent::get_object_or_404($holiday_event_id);
        $e->get_employee();
        try 
        {
            $e->employee->section = $this->external_query("SELECT * FROM sections WHERE id=".$e->employee->section_id)[0];
        }
        catch(Exception $e)
        {
            return $this->json([
                'type' => 'error',
                'data' => 'Nie znaleziono działu pracownika'
            ], 404);
        }

        $city = $this->get_users_city();
        $date = $city ? date('Y-m-d') : "";

        $mpdf = new mPDF('utf-8', [150, 90], '8', '', 1,1,2,1,1,5);
        $mpdf->useOnlyCoreFonts = true;
        ob_start();
        $html = "<div style='border: solid 1px black; margin: 5px; padding: 5px;'>
            <table style='width: 100%; text-align: center;'>
                <tr>
                    <td>....".$e->employee->first_name.' '.$e->employee->last_name."....</td>
                    <td>.....$city, $date.....</td>
                </tr>
                <tr>
                    <td><small style='font-size: 7px;'>Nazwisko i imię pracownika</small></td>
                    <td><small style='font-size: 7px;'>Miejscowośc i data</small></td>
                </tr>
                <tr>
                    <td>.....".$e->employee->section->name.".....</td>
                    <td></td>
                </tr>
                <tr>
                    <td><small style='font-size: 7px;'>Dział</small></td>
                    <td></td>
                </tr>
            </table>
            <br/><br/>
            <h3 style='text-align: center'>WNIOSEK O URLOP</h3>
            Proszę o udzielenie mi urlopu (zaznacz prawidłowe):
            <table style='text-align: left; width: 100%;'>
                <tr>
                    <td>".($e->kind_id == 1 ? "<b> x</b>" : '  ')." Wypoczynkowego</td>
                    <td>".($e->kind_id == 3 ? "<b> x</b>" : '  ')." Wypoczynkowego na żądanie</td>
                </tr>
                <tr>
                    <td>".($e->kind_id == 4 ? "<b> x</b>" : '  ')." Bezpłatngo</td>
                    <td>".($e->kind_id == 5 ? "<b> x</b>" : '  ')." Okolicznościowego</td>
                </tr>
                <tr>
                    <td>".($e->kind_id == 7 ? "<b> x</b>" : '  ')." W trybie art. 188 KP* (opieka na dziecko)</td>
                    <td>".($e->kind_id == 6 ? "<b> x</b>" : '  ')." Inny: ...".substr(explode('&lt;br/&gt;', $e->additional_info)[0], 13)."...</td>
                </tr>
            </table><br/>
            od dnia ....................".$e->from_date.".................... do dnia .....................".$e->to_date."....................<br/><br/><br/>
            <table style='width: 100%; text-align: center;'>
                <tr>
                    <td>....................................................</td>
                    <td>....................................................</td>
                </tr>
                <tr>
                    <td style='font-size: 7px;'>Podpis przełożonego</td>
                    <td style='font-size: 7px;'>Podpis pracownika</td>
                </tr>
            </table>
            <br/>
        </div>";

        $mpdf->WriteHTML($html);
        $html = ob_get_contents();
        ob_end_clean();
        $filepath = "statics/files/urlop.pdf";
        $content = $mpdf->Output($filepath, "F");
        $base64 = chunk_split(base64_encode(file_get_contents($filepath)));
        unlink($filepath);

        return $this->json([
            'type' => 'success',
            'data' => $base64
        ], 200);
    }
}

?>