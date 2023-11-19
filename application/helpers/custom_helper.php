<?php
function insert($table, $data)
{
    $ci = &get_instance();
    $ci->db->insert($table, $data);
    return $ci->db->insert_id();
}

function update($table, $where, $data)
{
    $ci = &get_instance();
    $ci->db->where($where);
    $ci->db->update($table, $data);
    return $ci->db->affected_rows();
}

function delete($table, $where)
{
    $ci = &get_instance();
    $ci->db->where($where);
    $ci->db->delete($table);
    return $ci->db->affected_rows();
}

function getRow($table, $where = array(), $select = '*')
{
    $CI = &get_instance();
    $CI->db->select($select);
    $CI->db->from($table);
    $CI->db->where($where);
    return $CI->db->get()->row_array();
}

function getRows($table, $where = array(), $select = '*')
{
    $ci = &get_instance();
    $ci->db->select($select);
    $ci->db->where($where);
    $ci->db->from($table);
    return $ci->db->get()->result_array();
}

function convertToSEO($text)
{
    $turkce  = array("ç", "Ç", "ğ", "Ğ", "ü", "Ü", "ö", "Ö", "ı", "İ", "ş", "Ş", ".", ",", "!", "'", "\"", " ", "?", "*", "_", "|", "=", "(", ")", "[", "]", "{", "}");
    $convert = array("c", "c", "g", "g", "u", "u", "o", "o", "i", "i", "s", "s", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");
    return strtolower(str_replace($turkce, $convert, $text));
}

function trStrToLower($text)
{
    $search = array("Ç", "Ğ", "İ", "Ö", "Ş", "Ü", "I");
    $replace = array("ç", "ğ", "i", "ö", "ş", "ü", "ı");
    $text = str_replace($search, $replace, $text);
    $text = strtolower($text);
    return $text;
}

function trUpperFirst($text)
{
    $text = trStrToLower($text);
    $text = mb_convert_case($text, MB_CASE_TITLE, "UTF-8");
    return $text;
}


function bracketsDel($text)
{
    $turkce  = array("(", ")", " ");
    $convert = array("", "", "-");
    return strtolower(str_replace($turkce, $convert, $text));
}

function sendMail($to, $subject, $message)
{
    $ci = &get_instance();
    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'mail.yandex.com';
    $config['smtp_user'] = 'user';
    $config['smtp_pass'] = 'pass';
    $config['smtp_port'] = '587';
    $config['sender'] = 'İbrahim KONUK';
    $config['charset'] = 'utf-8';
    $config['mailtype'] = 'html';
    $config['wordwrap'] = TRUE;
    $ci->email->initialize($config);
    $ci->email->from($config['smtp_user'], $config['sender']);
    $ci->email->to($to);
    $ci->email->subject($subject);
    $ci->email->message($message);

    if ($ci->email->send()) {
        return TRUE;
    } else {
        return FALSE;
    }
}
function createToken($bytes = 8)
{
    return md5(bin2hex(openssl_random_pseudo_bytes($bytes)));
}

function deleteExpiredFiles()
{
    $ci = &get_instance();
    $mydir = $ci->config->item('sess_save_path') . '/';
    $myfiles = array_diff(scandir($mydir), array('.', '..'));
    foreach ($myfiles as $file) {
        $fileInfo = pathinfo($mydir . $file);
        if (@$fileInfo['extension'] == NULL) {
            $created = new DateTime(date("d-m-Y H:i:s.", filectime($mydir . $file)));
            $now = new DateTime(date('d-m-Y H:i:s'));
            $interval = date_diff($created, $now);
            $fark = $interval->format('%s');
            if ($fark > $ci->config->item('sess_expiration')) {
                unlink($mydir . $file);
            }
        }
    }
    $mydir = $ci->config->item('log_path');
    $myfiles = array_diff(scandir($mydir), array('.', '..'));
    foreach ($myfiles as $file) {
        $fileInfo = pathinfo($mydir . $file);
        if (@$fileInfo['extension'] == 'txt') {
            $created = new DateTime(date("d-m-Y H:i:s.", filectime($mydir . $file)));
            $now = new DateTime(date('d-m-Y H:i:s'));
            $interval = date_diff($created, $now);
            $fark = $interval->format('%d');
            if ($fark > 15) {
                unlink($mydir . $file);
            }
        }
    }
}


function pre($data = array(), $bool = 0)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if ($bool == 1) {
        die;
    }
}

function curLRequest($url, $header = array())
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_ENCODING => "",
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true
    );
    if (!empty($header)) {
        $options[CURLOPT_HTTPHEADER] = $header;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);
    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;
    return $header['content'];
}

function numberFormat($number)
{
    return number_format($number, 2, '.', '');
}

if (!function_exists('ara')) {
    function ara($bas, $son, $yazi)
    {
        @preg_match_all('/' . preg_quote($bas, '/') .
            '(.*?)' . preg_quote($son, '/') . '/i', $yazi, $m);
        return @$m[1];
    }
}

function ara2($start, $end, $data)
{
    $matches = array();
    $pattern = '/' . preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/') . '/s';

    preg_match_all($pattern, $data, $matches);

    if (!empty($matches[1])) {
        return $matches[1];
    } else {
        return array();
    }
}

function removeLinks($html)
{
    $cleanedHtml = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '$1', $html);
    return $cleanedHtml;
}

function getDescriptionImages($html)
{
    $images = array();
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $src = $xpath->evaluate("string(//img/@src)");
    $images[] = $src;
    return $images;
}

function checkGTIN($gtin)
{
    // Boşlukları ve tireleri kaldırarak sadece sayıları al
    $gtin = preg_replace('/[^0-9]/', '', $gtin);

    // GTIN, 12, 13 veya 14 haneli bir sayı dizisi olmalıdır
    if (preg_match('/^\d{12}$/', $gtin) || preg_match('/^\d{13}$/', $gtin) || preg_match('/^\d{14}$/', $gtin)) {
        // GTIN numarası kurallarına uyuyor
        return true;
    } else {
        // GTIN numarası kurallarına uymuyor
        return false;
    }
}

function currency()
{
    $xml = file_get_contents('https://www.tcmb.gov.tr/kurlar/today.xml');
    $array = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);
    $data['USD'] = $array['Currency'][0]['BanknoteSelling'];
    $data['EUR'] = $array['Currency'][3]['BanknoteSelling'];
    return $data;
}

function XMLDownload($supplierID = null)
{
    $supplier = getRow('suppliers', array('id' => $supplierID));
    $url = $supplier['xml'];

    $xml = file_get_contents($url);
    //$xmlName = date('d-m-Y-H-i-s') . createToken(4) . '-' . $bayi . '.xml';
    if (file_put_contents('uploads/xml/' . $supplier['supplierSlug'] . '/' . $supplier['supplierSlug'] . '.xml', $xml)) {
        return true;
    } else {
        return false;
    }
}

function validateEAN($ean)
{
    // EAN-13 numarası tam olarak 13 karakter içermelidir.
    if (preg_match('/^[0-9]{13}$/', $ean)) {
        // EAN-13 numarasının son rakamını çıkartın
        $ean12 = substr($ean, 0, 12);

        // Çift pozisyonlu ve tek pozisyonlu rakamları ayırın
        $evenDigits = $oddDigits = 0;
        for ($i = 0; $i < 12; $i++) {
            if ($i % 2 == 0) {
                $oddDigits += $ean12[$i];
            } else {
                $evenDigits += $ean12[$i];
            }
        }

        // Toplamı 3 ile çarpın ve çift pozisyonlu rakamları ekleyin
        $checksum = (3 * $oddDigits) + $evenDigits;

        // Son rakamın tamamı 10'a tam bölünmelidir
        $checksum %= 10;
        $checksum = 10 - $checksum;
        $checksum %= 10;

        // Son rakam, hesaplanan kontrol rakamı ile aynı olmalıdır
        return $checksum == $ean[12];
    } else {
        return false;
    }
}

function json($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    die;
}

function getProductsHB()
{
    $ci = &get_instance();
    $ci->db->select('products.*, product_prices.*');
    $ci->db->from('products');
    $ci->db->join('suppliers', 'suppliers.id = products.supplierID');
    $ci->db->join('product_prices', 'product_prices.stockCode = products.stockCode');
    $ci->db->order_by('product_prices.hbCurlReview', 'DESC');
    $data =  $ci->db->get()->result_array();
    return $data;
}

function checkURL($url)
{
    $pattern = '/^https:\/\/www\.hepsiburada\.com\/[a-zA-Z0-9-]+-c-[0-9]+$/';

    if (preg_match($pattern, $url)) {
        return true;
    } else {
        return false;
    }
}
