<?php

/**
 * Namaz - Diyanet İşleri Başkanlığından veri çekme sınıfı
 *
 * @author		Erdem ARSLAN (http://www.erdemarslan.com/)
 * @copyright	Copyright (c) 2014 erdemarslan.com
 * @link		http://www.erdemarslan.com/programlama/php-programlama/06-01-2014/563-namaz-vakitleri-php-sinifi.html
 * @version     5.0
 * @license		GPL v2.0
 */

Class Namaz
{
	
	protected $ulke		= 2;
	protected $sehir	= 539;
	protected $ilce		= 9541;
	
	protected $cache_klasoru = 'cache';
	protected $cache;
	
	
	protected $ulke_isimleri = array();
	
	protected $ulkeler;
	protected $sehirler;
	protected $ilceler;
	
	protected $server;
	
	
	
	protected $hicriaylar = array(
		0 => '',
		1 => 'Muharrem',
		2 => 'Safer',
		3 => "Rebiü'l-Evvel",
		4 => "Rebiü'l-Ahir",
		5 => "Cemaziye'l-Evvel",
		6 => "Cemaziye'l-Ahir",
		7 => 'Recep',
		8 => 'Şaban',
		9 => 'Ramazan',
		10 => 'Sevval',
		11 => "Zi'l-ka'de",
		12 => "Zi'l-Hicce"
	);
		
	/**
     * Sınıfı yapılandırıcı fonksiyon
     *
     * @return mixed
     */
	public function __construct($cache_klasoru=NULL, $hicriaylar=null, $ulke_isimleri =null)
	{
		// Cache yolunu belirleyelim!
		$dosyayolu = dirname(__FILE__);
		$this->cache = is_null( $cache_klasoru ) === TRUE ? $dosyayolu . DIRECTORY_SEPARATOR . $this->cache_klasoru . DIRECTORY_SEPARATOR : $cache_klasoru;
		
		// hicri ayların dillenmiş halini al!
		$this->hicriaylar = is_null( $hicriaylar ) === TRUE ? $this->hicriaylar : $hicriaylar;
		// ülke isimleri verilmişse al!
		$this->ulke_isimleri = is_null( $ulke_isimleri ) === TRUE ? $this->ulke_isimleri : $ulke_isimleri;
		
		// cacheden ülke şehir ve ilçeleri oku!
		$this->ulkeler	= file_get_contents( $dosyayolu . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'ulkeler.ndb' );
		$this->sehirler	= file_get_contents( $dosyayolu . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'sehirler.ndb' );
		$this->ilceler	= file_get_contents( $dosyayolu . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'ilceler.ndb' );
		
	}
	
		
	#####################################################################################################################
	#####											VERİ VERME İŞLEMLERİ											#####
	#####################################################################################################################
	
	/**
     * Ülkesi verilen şehirleri çeker
     *
     * @param string Verisi çekilecek ülkeyi belirler
	 * @param string Verinin dışarıya nasıl çıktılanacağını belirtir
     * @return array Sonucu bir dizi olarak döndürür
     */
	public function ulkeler( $cikti='array' )
	{
		// ülkeleri arraya çevir
		$ulkeler = json_decode( $this->ulkeler, TRUE);
		$sonuc = array(
			'durum' => 'hata',
			'veri' => array()
		);
		
		foreach( $ulkeler as $key => $value )
		{
			$sonuc['durum'] = 'basarili';
			$sonuc['veri'][$key] = array_key_exists( $value, $this->ulke_isimleri ) === TRUE ? $this->ulke_isimleri[$value] : $value;
		}
		
		$yazdir = $cikti == 'array' ? $sonuc : json_encode( $sonuc );
		return $yazdir;
	}
	
	/**
     * Ülkesi verilen şehirleri çeker
     *
     * @param string Verisi çekilecek ülkeyi belirler
	 * @param string Verinin dışarıya nasıl çıktılanacağını belirtir
     * @return array Sonucu bir dizi olarak döndürür
     */
	public function sehirler( $ulke=NULL, $cikti='array' )
	{
		$ulke = is_null( $ulke ) === TRUE ? $this->ulke : $ulke;
		
		// şehirleri arraya çevir
		$sehirler = json_decode( $this->sehirler, TRUE);
		
		$sonuc = array(
			'durum' => 'hata',
			'veri' => array()
		);
		
		if ( array_key_exists( $ulke, $sehirler ) )
		{
			$sonuc['durum'] = 'basarili';
			$sonuc['veri'] = $sehirler[$ulke];
		}
		
		$yazdir = $cikti == 'array' ? $sonuc : json_encode( $sonuc );
		return $yazdir;
	}
	
	/**
     * Şehri verilen ilçeleri çeker
     *
     * @param string Verisi çekilecek şehri belirler
	 * @param string Verinin dışarıya nasıl çıktılanacağını belirtir
     * @return array Sonucu bir dizi olarak döndürür
     */
	public function ilceler( $sehir=NULL, $cikti='array' )
	{
		$sehir = is_null( $sehir ) === TRUE ? $this->sehir : $sehir;
		
		// ilçeleri alalım
		$ilceler = json_decode( $this->ilceler, TRUE );
		
		$sonuc = array(
			'durum' => 'hata',
			'veri' => array()
		);
		
		if( array_key_exists( $sehir, $ilceler ) )
		{
			$sonuc['durum'] = 'basarili';
			$sonuc['veri'] = $ilceler[$sehir];
		}
		
		
		$yazdir = $cikti == 'array' ? $sonuc : json_encode( $sonuc );
		return $yazdir;
	}
	
	/**
     * Verilen ülke ve şehir için vakitleri çeker
     *
     * @param string Verisi çekilecek ülkeyi belirler
	 * @param string Verisi çekilecek şehiri belirler
	 * @param string Verinin dışarıya nasıl çıktılanacağını belirtir
     * @return array Sonucu bir dizi olarak döndürür
     */
	public function vakit( $ulke=NULL, $sehir=NULL, $ilce=NULL, $cikti='array' )
	{
		$sehir = is_null( $sehir ) === TRUE ? $this->sehir : $sehir;
		$ulke = is_null( $ulke ) === TRUE ? $this->ulke : $ulke;
		if ($ulke == 2 || $ulke == 33 || $ulke == 52)
		{
			$ilce = is_null( $ilce ) === TRUE ? $this->ilce : $ilce;
		} else {
			$ilce = is_null( $ilce ) === TRUE ? $this->sehir : $ilce;
		}
		
		if( $this->__cache_sor( 'vakit_' . $ulke . '_' . $sehir . '_' . $ilce, 1 ) )
		{
			$sonuc = $this->__cache_oku( 'vakit_' . $ulke . '_' . $sehir . '_' . $ilce );
		} else {
			$veri = $this->al_vakitler( $ulke, $sehir, $ilce );
			
			if( $veri['durum'] == 'basarili' )
			{
				$this->__cache_yaz( 'vakit_' . $ulke . '_' . $sehir . '_' . $ilce , json_encode($veri) );
			}
			$sonuc = json_encode( $veri );
		}
		$yazdir = $cikti == 'json' ? $sonuc : json_decode( $sonuc, TRUE );
		return $yazdir;
	}
	
	
	#####################################################################################################################
	#####												CACHE İŞLEMLERİ												#####
	#####################################################################################################################
	
	/**
     * Cache dosyası var mı yok mu, varsa süresi geçerli mi onu kontrol eder!
     *
     * @param string Dosyanın adı
	 * @param integer 0 - süresiz, 1 - 1 gün süreli
     * @return boolean Sonuç TRUE ya da FALSE olarak döner.
     */
	private function __cache_sor( $dosya, $gecerli=0 )
	{
		if ( file_exists( $this->cache .  $dosya . '.json' ) AND is_readable( $this->cache . $dosya . '.json' ) )
		{
			if ( $gecerli == 0 )
			{
				return TRUE;
			} else {
				$dosya_zamani = date( 'dmY', filemtime( $this->cache . $dosya . '.json' ) );
				$bugun = date( 'dmY', time() );
				
				if ( $dosya_zamani == $bugun )
				{
					return TRUE;
				} else {
					return FALSE;
				}
			}
		} else {
			return FALSE;
		}
	}
	
	/**
     * Cache dosyasından okur
     *
     * @param string Dosyanın adı
     * @return json Sonuç json türünde geri döner
     */
	private function __cache_oku( $dosya )
	{
		return file_get_contents( $this->cache . $dosya . '.json' );
	}
	
	/**
     * Cache dosyasına yazar
     *
     * @param string Dosyanın adı
	 * @param string Dosyaya kaydedilecek veri
     * @return mixed Sonuç dönmez
     */
	private function __cache_yaz( $dosya , $veri )
	{
		$fp = fopen( $this->cache . $dosya . '.json', "w" );
		fwrite( $fp, $veri );
		fclose( $fp );
		return;
	}
	
		
	
	#####################################################################################################################
	#####											VERİ ÇEKME İŞLEMLERİ											#####
	#####################################################################################################################
	
	
	/**
     * Verilen ülke ve şehir için vakitleri çeker
     *
     * @param string Verisi çekilecek ülkeyi belirler
	 * @param string Verisi çekilecek şehiri belirler
     * @return array Sonucu bir dizi olarak döndürür
     */
	private function al_vakitler( $ulke=NULL, $sehir=NULL, $ilce = NULL )
	{
		$ulke = is_null( $ulke ) === TRUE ? $this->ulke : $ulke;
		$sehir = is_null( $sehir ) === TRUE ? $this->ilce : $sehir;
		if ($ulke == 2 || $ulke == 33 || $ulke == 52)
		{
			$ilce = is_null( $ilce ) === TRUE ? $this->ilce : $ilce;
		} else {
			$ilce = is_null( $ilce ) === TRUE ? $sehir : $ilce;
		}
		
		$this->server_check();
		
		$url =  $this->server . '/PrayerTime/PrayerTimesSet';
		
		$data = array(
			"countryName"	=> "$ulke",
			"name"			=> "$ilce",
			"stateName"		=> "$sehir"
		);
		
		$data = json_encode( $data );
		
		$sonuc = $this->__curl( $url, $data, TRUE );
		
		$karaliste = array('NextImsak', 'GunesText', 'ImsakText', 'OgleText', 'IkindiText', 'AksamText', 'YatsiText', 'HolyDaysItem');
		
		if ( $sonuc['durum'] == 'basarili' )
		{
			$ulkeler = $this->ulkeler();
			$sehirler = $this->sehirler($ulke);
			$ilceler = $this->ilceler($sehir);
			
			$ulke_adi = $ulkeler['veri'][$ulke];
			$sehir_adi = $sehirler['veri'][$sehir];
			$ilce_adi = $ilceler['veri'][$ilce];
			
			
			
			if ($sehir_adi == $ilce_adi)
			{
				$yer_adi =  $ulke_adi . '<br>' . $sehir_adi;
			} else {
				
				$yer_adi = $sehir_adi . '<br>' . $ilce_adi;
			}
			
			$veri = array(
				'yer_adi' => $yer_adi
			);
			foreach ( $sonuc['veri'] as $k=>$v )
			{
				if( !in_array($k, $karaliste ) )
				{
					if ( $k == 'MoonSrc' )
					{
						$veri[strtolower($k)] = $this->server . '/UserFiles/AyEvreleri/' . $v;
					}
					elseif ( $k == 'HicriTarih' )
					{
						$veri[strtolower($k)] = $this->hicri();
					} else {
						$veri[strtolower($k)] = $v;
					}
				}
			}
			$sonuc['veri'] = $veri;
		}
		
		return $sonuc;
	}
	
	/**
     * Sunucu kontrol metodu - Diyanetin Hac kuralarını belirlemesiyle ortaya çıktı - Özeldir
     *
     * @param none
     * @return $this
     */
	 
	private function server_check()
	{
	 	$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'http://www.diyanet.gov.tr/tr/namazvakitleri' );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, FALSE );

		$bilgi = curl_getinfo( $ch );
		curl_close( $ch );
		
		$this->server = $bilgi['http_code'] == 200 ? 'http://www.diyanet.gov.tr' : 'http://web2.diyanet.gov.tr';
		
		return $this;
	}
		
	
	/**
     * Diyanetten verileri almak için cURL metodu - Özeldir
     *
     * @param string Bağlantı adresini verir
     * @param string Başlantı için gerekli verileri verir
	 * @param boolean Bu bağlantının POST metodu ile yapılıp yapılmayacağını belirtir
     * @return array sonucu bir dizi olarak döndürür
     */
	private function __curl($url, $data, $is_post=FALSE)
	{
		if( !$is_post )
		{
			$url = sprintf( $url, $data );
		}
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		
		// Post varsa 
		if ( $is_post )
		{
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen( $data ) ) );
		}
			
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		
		$bilgi = curl_getinfo( $ch );
		$veri = curl_exec( $ch );
				
		if( $bilgi['http_code'] == 200 ) // POST durumunda geçerli veri dönerse HTTP_RESPONSE_CODE = 200 oluyor!
		{
			
			$sonuc = array(
				'durum'	=> 'basarili',
				'veri'	=> json_decode( $veri, TRUE )
			);
		}
		elseif ($bilgi['http_code'] == 0 )
		{
			// GET Durumunda HTTP_RESPONSE_CODE = 0 olduğundan gelen veriye bakıyoruz. Eğer [] ise hata, değil ise veri!
			if( $veri != '[]' )
			{
				$sonuc = array(
					'durum'	=> 'basarili',
					'veri'	=> json_decode( $veri, TRUE )
				);
			} else {
				$sonuc = array(
					'durum'	=> 'hata',
					'veri'	=> array()
				);
			}
		}
		else
		{
			$sonuc = array(
				'durum'	=> 'hata',
				'veri'	=> array()
			);
		}
		curl_close( $ch );
		return $sonuc;
	}
	
	
	#####################################################################################################################
	#####										HİCRİ TAKVIM FONKSIYONLARI											#####
	#####################################################################################################################
	
	private function hicri( $tarih = null )
	{
		if ($tarih === null) $tarih = date('d.m.Y',time());
		$t = explode('.',$tarih);
		
		$h = new HijriDateTime;
		$bugun = $h->GeToHijr($t[0], $t[1], $t[2]);
		
		return $bugun['day'] . ' ' . __($this->hicriaylar[$bugun['month']], 'namazvakti') . ' ' . $bugun['year'];
	}
	
	/*
	private function hicri($tarih = null)
	{
		if ($tarih === null) $tarih = date('d.m.Y',time());
		$t = explode('.',$tarih);
		
		return $this->jd2hicri(cal_to_jd(CAL_GREGORIAN, $t[1],$t[0],$t[2]));
	}
	
	private function miladi($tarih = null)
	{
		if ($tarih === null) $tarih = date('d.m.Y',time());
		$t = explode('.',$tarih);
		return jd_to_cal(CAL_GREGORIAN,$this->hicri2jd($t[1],$t[0],$t[2]));
	}

    # julian day takviminden hicriye geçiş
    private function jd2hicri($jd)
    {
        $jd = $jd - 1948440 + 10632;
        $n  = (int)(($jd - 1) / 10631);
        $jd = $jd - 10631 * $n + 354;
        $j  = ((int)((10985 - $jd) / 5316)) *
            ((int)(50 * $jd / 17719)) +
            ((int)($jd / 5670)) *
            ((int)(43 * $jd / 15238));
        $jd = $jd - ((int)((30 - $j) / 15)) *
            ((int)((17719 * $j) / 50)) -
            ((int)($j / 16)) *
            ((int)((15238 * $j) / 43)) + 29;
        $m  = (int)(24 * $jd / 709);
        $d  = $jd - (int)(709 * $m / 24);
        $y  = 30*$n + $j - 30;

        return $d . ' ' . $this->hicriaylar[$m] . ' ' . $y;
    }

    # hicriden julian day takvimine geçiş
    private function hicri2jd($m, $d, $y)
    {
        return (int)((11 * $y + 3) / 30) +
            354 * $y + 30 * $m -
            (int)(($m - 1) / 2) + $d + 1948440 - 385;
    }
	*/
} // Sınıf Bitti

/**
 * Hijri Date Time Class
 * by: Abdulrhman Alkhodiry
 * --------------------
 * Total set for conversion between Hijri (Islamic) and 
 * Gregorian calendars. introducing new date() mktime() functions 
 * with FULL Arabic translation for both Hijri & Gregorian dates. 
 * This class is 100% compatible with "Umm al-Qura" formal calendar in Saudi Arabia and the Muslim world.
 * --------------------
 * LICENSE
 * -------
 * This program is open source product; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License (GPL)
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * To read the license please visit http://www.gnu.org/copyleft/gpl.html
 * 
 * --------------------
 * Thanks to: Abdul-Aziz Al-Oraij <aziz.oraij.com>
 * --------------------
 * @package    HijriDateTime
 * @author     Abdulrhman Alkhodiry <zeroows@gmail.com
 * @copyright  2012 Abdulrhman Alkhodiry
 * @license    http://www.gnu.org/copyleft/gpl.html  GNU General Public License (GPL)
 * @link       http://7in.org
 * @see        DateTime
 * @version    1.0.1
 * 
 *  Bism Allah..
 */
class HijriDateTime {

    /**
     * Defaults
     */
    private $jdl = Array(8761, 8790, 8820, 8849, 8879, 8908, 8937, 8967, 8996, 9026, 9055, 9085, 9115, 9144, 9174, 9204, 9233, 9263, 9292, 9321, 9351, 9380, 9409, 9439, 9469, 9498, 9528, 9558, 9588, 9617, 9647, 9676, 9705, 9735, 9764, 9793, 9823, 9852, 9882, 9912, 9942, 9971, 10001, 10031, 10060, 10089, 10119, 10148, 10177, 10207, 10236, 10266, 10296, 10325, 10355, 10385, 10414, 10444, 10473, 10503, 10532, 10561, 10591, 10620, 10650, 10679, 10709, 10739, 10769, 10798, 10828, 10857, 10887, 10916, 10946, 10975, 11004, 11034, 11063, 11093, 11123, 11152, 11182, 11211, 11241, 11271, 11300, 11330, 11359, 11389, 11418, 11447, 11477, 11506, 11536, 11566, 11595, 11625, 11654, 11684, 11714, 11743, 11773, 11802, 11831, 11861, 11890, 11920, 11949, 11979, 12009, 12038, 12068, 12098, 12127, 12157, 12186, 12215, 12245, 12274, 12303, 12333, 12363, 12392, 12422, 12452, 12482, 12511, 12541, 12570, 12599, 12629, 12658, 12687, 12717, 12746, 12776, 12806, 12836, 12865, 12895, 12925, 12954, 12983, 13013, 13042, 13071, 13101, 13130, 13160, 13190, 13219, 13249, 13279, 13309, 13338, 13367, 13397, 13426, 13455, 13485, 13514, 13544, 13573, 13603, 13633, 13663, 13692, 13722, 13751, 13781, 13810, 13839, 13869, 13898, 13928, 13957, 13987, 14017, 14046, 14076, 14106, 14135, 14165, 14194, 14223, 14253, 14282, 14312, 14341, 14371, 14400, 14430, 14460, 14489, 14519, 14549, 14578, 14608, 14637, 14666, 14696, 14725, 14755, 14784, 14814, 14843, 14873, 14903, 14932, 14962, 14992, 15021, 15051, 15080, 15109, 15139, 15168, 15198, 15227, 15257, 15286, 15316, 15346, 15376, 15405, 15435, 15464, 15493, 15523, 15552, 15581, 15611, 15640, 15670, 15700, 15730, 15759, 15789, 15819, 15848, 15877, 15907, 15936, 15965, 15995, 16024, 16054, 16084, 16113, 16143, 16173, 16203, 16232, 16261, 16291, 16320, 16349, 16379, 16408, 16438, 16467, 16497, 16527, 16557, 16586, 16616, 16645, 16675, 16704, 16733, 16763, 16792, 16822, 16851, 16881, 16911, 16940, 16970, 17000, 17029, 17059, 17088, 17117, 17147, 17176, 17206, 17235, 17265, 17294, 17324, 17354, 17383, 17413, 17443, 17472, 17501, 17531, 17560, 17590, 17619, 17649, 17678, 17708, 17737, 17767, 17797, 17826, 17856, 17885, 17915, 17944, 17974, 18003, 18033, 18062, 18092, 18121, 18151, 18180, 18210, 18240, 18269, 18299, 18329, 18358, 18387, 18417, 18446, 18475, 18505, 18534, 18564, 18594, 18624, 18653, 18683, 18713, 18742, 18771, 18801, 18830, 18859, 18889, 18918, 18948, 18978, 19007, 19037, 19067, 19097, 19126, 19155, 19185, 19214, 19243, 19273, 19302, 19332, 19361, 19391, 19421, 19451, 19480, 19510, 19539, 19569, 19598, 19627, 19657, 19686, 19716, 19745, 19775, 19805, 19835, 19864, 19894, 19923, 19953, 19982, 20011, 20041, 20070, 20100, 20129, 20159, 20188, 20218, 20248, 20278, 20307, 20337, 20366, 20395, 20425, 20454, 20484, 20513, 20543, 20572, 20602, 20632, 20661, 20691, 20720, 20750, 20779, 20809, 20838, 20868, 20897, 20927, 20956, 20986, 21015, 21045, 21075, 21104, 21134, 21163, 21193, 21222, 21252, 21281, 21311, 21340, 21370, 21399, 21429, 21458, 21488, 21518, 21547, 21577, 21606, 21636, 21665, 21695, 21724, 21753, 21783, 21812, 21842, 21872, 21901, 21931, 21961, 21990, 22020, 22049, 22079, 22108, 22137, 22167, 22196, 22226, 22255, 22285, 22315, 22345, 22374, 22404, 22433, 22463, 22492, 22521, 22551, 22580, 22610, 22639, 22669, 22699, 22729, 22758, 22788, 22817, 22847, 22876, 22905, 22935, 22964, 22994, 23023, 23053, 23083, 23112, 23142, 23172, 23201, 23231, 23260, 23289, 23319, 23348, 23378, 23407, 23437, 23466, 23496, 23526, 23555, 23585, 23614, 23644, 23673, 23703, 23732, 23762, 23791, 23821, 23850, 23880, 23909, 23939, 23969, 23998, 24028, 24057, 24087, 24116, 24146, 24175, 24205, 24234, 24264, 24293, 24323, 24352, 24382, 24412, 24441, 24471, 24500, 24530, 24559, 24589, 24618, 24647, 24677, 24706, 24736, 24766, 24795, 24825, 24855, 24884, 24914, 24943, 24973, 25002, 25031, 25061, 25090, 25120, 25149, 25179, 25209, 25239, 25268, 25298, 25327, 25357, 25386, 25415, 25445, 25474, 25504, 25533, 25563, 25593, 25623, 25652, 25682, 25711, 25741, 25770, 25799, 25829, 25858, 25887, 25917, 25947, 25977, 26006, 26036, 26066, 26095, 26125, 26154, 26183, 26213, 26242, 26271, 26301, 26331, 26360, 26390, 26420, 26450, 26479, 26509, 26538, 26567, 26597, 26626, 26655, 26685, 26714, 26744, 26774, 26804, 26833, 26863, 26892, 26922, 26951, 26981, 27010, 27040, 27069, 27098, 27128, 27158, 27187, 27217, 27247, 27276, 27306, 27335, 27365, 27394, 27424, 27453, 27483, 27512, 27541, 27571, 27601, 27630, 27660, 27690, 27719, 27749, 27778, 27808, 27837, 27867, 27896, 27925, 27955, 27984, 28014, 28044, 28073, 28103, 28133, 28162, 28192, 28221, 28251, 28280, 28309, 28339, 28368, 28398, 28427, 28457, 28487, 28517, 28546, 28576, 28605, 28635, 28664, 28693, 28723, 28752, 28781, 28811, 28841, 28870, 28900, 28930, 28960, 28989, 29019, 29048, 29077, 29107, 29136, 29165, 29195, 29225, 29254, 29284, 29314, 29344, 29373, 29403, 29432, 29461, 29491, 29520, 29549, 29579, 29608, 29638, 29668, 29698, 29727, 29757, 29787, 29816, 29845, 29875, 29904, 29933, 29963, 29992, 30022, 30052, 30081, 30111, 30141, 30170, 30200, 30229, 30259, 30288, 30318, 30347, 30376, 30406, 30435, 30465, 30495, 30524, 30554, 30584, 30613, 30643, 30672, 30702, 30731, 30760, 30790, 30819, 30849, 30878, 30908, 30938, 30967, 30997, 31027, 31056, 31086, 31115, 31145, 31174, 31203, 31233, 31262, 31292, 31321, 31351, 31381, 31410, 31440, 31470, 31499, 31529, 31558, 31587, 31617, 31646, 31675, 31705, 31735, 31764, 31794, 31824, 31854, 31883, 31913, 31942, 31971, 32001, 32030, 32059, 32089, 32119, 32148, 32178, 32208, 32238, 32267, 32297, 32326, 32355, 32385, 32414, 32443, 32473, 32502, 32532, 32562, 32592, 32621, 32651, 32681, 32710, 32739, 32769, 32798, 32827, 32857, 32886, 32916, 32946, 32975, 33005, 33035, 33064, 33094, 33123, 33153, 33182, 33211, 33241, 33270, 33300, 33329, 33359, 33389, 33419, 33448, 33478, 33507, 33537, 33566, 33596, 33625, 33654, 33684, 33713, 33743, 33773, 33802, 33832, 33861, 33891, 33921, 33950, 33980, 34009, 34038, 34068, 34097, 34127, 34156, 34186, 34215, 34245, 34275, 34304, 34334, 34364, 34393, 34423, 34452, 34481, 34511, 34540, 34570, 34599, 34629, 34659, 34688, 34718, 34748, 34777, 34807, 34836, 34865, 34895, 34924, 34953, 34983, 35013, 35042, 35072, 35102, 35132, 35161, 35191, 35220, 35249, 35279, 35308, 35337, 35367, 35396, 35426, 35456, 35486, 35515, 35545, 35575, 35604, 35633, 35663, 35692, 35721, 35751, 35780, 35810, 35840, 35869, 35899, 35929, 35958, 35988, 36017, 36047, 36076, 36105, 36135, 36164, 36194, 36223, 36253, 36283, 36313, 36342, 36372, 36401, 36431, 36460, 36489, 36519, 36548, 36578, 36607, 36637, 36667, 36696, 36726, 36756, 36785, 36815, 36844, 36873, 36903, 36932, 36962, 36991, 37021, 37050, 37080, 37110, 37139, 37169, 37198, 37228, 37258, 37287, 37316, 37346, 37375, 37405, 37434, 37464, 37493, 37523, 37553, 37582, 37612, 37642, 37671, 37701, 37730, 37759, 37789, 37818, 37847, 37877, 37907, 37936, 37966, 37996, 38025, 38055, 38085, 38114, 38143, 38173, 38202, 38231, 38261, 38290, 38320, 38350, 38380, 38409, 38439, 38469, 38498, 38527, 38557, 38586, 38615, 38645, 38674, 38704, 38734, 38763, 38793, 38823, 38853, 38882, 38911, 38941, 38970, 38999, 39029, 39058, 39088, 39117, 39147, 39177, 39207, 39236, 39266, 39295, 39325, 39354, 39383, 39413, 39442, 39472, 39501, 39531, 39561, 39590, 39620, 39650, 39679, 39709, 39738, 39767, 39797, 39826, 39856, 39885, 39915, 39944, 39974, 40004, 40033, 40063, 40092, 40122, 40151, 40181, 40210, 40240, 40269, 40299, 40328, 40358, 40387, 40417, 40447, 40476, 40506, 40535, 40565, 40594, 40624, 40653, 40683, 40712, 40742, 40771, 40801, 40830, 40860, 40890, 40919, 40949, 40978, 41008, 41037, 41067, 41096, 41125, 41155, 41184, 41214, 41244, 41274, 41303, 41333, 41363, 41392, 41421, 41451, 41480, 41509, 41539, 41568, 41598, 41628, 41657, 41687, 41717, 41747, 41776, 41805, 41835, 41864, 41893, 41923, 41952, 41982, 42011, 42041, 42071, 42101, 42130, 42160, 42189, 42219, 42248, 42277, 42307, 42336, 42366, 42395, 42425, 42455, 42484, 42514, 42544, 42573, 42603, 42632, 42661, 42691, 42720, 42750, 42779, 42809, 42838, 42868, 42898, 42928, 42957, 42987, 43016, 43045, 43075, 43104, 43134, 43163, 43193, 43222, 43252, 43282, 43311, 43341, 43370, 43400, 43429, 43459, 43488, 43518, 43547, 43577, 43606, 43636, 43665, 43695, 43725, 43754, 43784, 43813, 43843, 43872, 43902, 43931, 43961, 43990, 44019, 44049, 44079, 44108, 44138, 44168, 44197, 44227, 44256, 44286, 44315, 44345, 44374, 44403, 44433, 44462, 44492, 44522, 44551, 44581, 44611, 44640, 44670, 44699, 44729, 44758, 44787, 44817, 44846, 44876, 44905, 44935, 44965, 44995, 45024, 45054, 45083, 45113, 45142, 45171, 45201, 45230, 45260, 45289, 45319, 45349, 45379, 45408, 45438, 45467, 45497, 45526, 45555, 45585, 45614, 45643, 45673, 45703, 45732, 45762, 45792, 45822, 45851, 45881, 45910, 45939, 45969, 45998, 46027, 46057, 46087, 46116, 46146, 46176, 46205, 46235, 46264, 46294, 46323, 46353, 46382, 46412, 46441, 46471, 46500, 46530, 46559, 46589, 46619, 46648, 46678, 46707, 46737, 46766, 46796, 46825, 46855, 46884, 46914, 46943, 46973, 47002, 47032, 47062, 47091, 47121, 47150, 47180, 47209, 47239, 47268, 47297, 47327, 47356, 47386, 47416, 47445, 47475, 47505, 47534, 47564, 47593, 47623, 47652, 47681, 47711, 47740, 47770, 47799, 47829, 47859, 47889, 47918, 47948, 47977, 48007, 48036, 48065, 48095, 48124, 48154, 48183, 48213, 48243, 48273, 48302, 48332, 48361, 48391, 48420, 48449, 48479, 48508, 48537, 48567, 48597, 48626, 48656, 48686, 48716, 48745, 48775, 48804, 48833, 48863, 48892, 48921, 48951, 48981, 49010, 49040, 49070, 49099, 49129, 49159, 49188, 49217, 49247, 49276, 49305, 49335, 49364, 49394, 49424, 49453, 49483, 49513, 49542, 49572, 49601, 49631, 49660, 49690, 49719, 49748, 49778, 49808, 49837, 49867, 49897, 49926, 49956, 49985, 50015, 50044, 50074, 50103, 50132, 50162, 50191, 50221, 50251, 50280, 50310, 50339, 50369, 50399, 50428, 50458, 50487, 50517, 50546, 50575, 50605, 50634, 50664, 50693, 50723, 50753, 50783, 50812, 50842, 50871, 50901, 50930, 50959, 50989, 51018, 51048, 51077, 51107, 51137, 51166, 51196, 51226, 51255, 51285, 51314, 51344, 51373, 51402, 51432, 51461, 51491, 51521, 51551, 51581, 51610, 51640, 51669, 51698, 51728, 51757, 51786, 51815, 51845, 51875, 51905, 51935, 51964, 51994, 52024, 52053, 52082, 52112, 52141, 52170, 52199, 52229, 52259, 52289, 52318, 52348, 52378, 52407, 52437, 52466, 52496, 52525, 52554, 52584, 52613, 52643, 52672, 52702, 52732, 52761, 52791, 52821, 52850, 52880, 52909, 52938, 52968, 52997, 53027, 53056, 53086, 53115, 53145, 53175, 53204, 53234, 53263, 53293, 53323, 53352, 53382, 53411, 53440, 53470, 53499, 53529, 53558, 53588, 53618, 53647, 53677, 53707, 53736, 53766, 53795, 53824, 53854, 53883, 53912, 53942, 53972, 54002, 54031, 54061, 54091, 54120, 54150, 54179, 54208, 54238, 54267, 54296, 54326, 54356, 54386, 54415, 54445, 54475, 54504, 54534, 54563, 54592, 54622, 54651, 54680, 54710, 54740, 54769, 54799, 54829, 54858, 54888, 54918, 54947, 54976, 55006, 55035, 55065, 55094, 55124, 55153, 55183, 55212, 55242, 55272, 55301, 55331, 55360, 55390, 55419, 55449, 55478, 55507, 55537, 55566, 55596, 55626, 55656, 55685, 55715, 55744, 55774, 55803, 55833, 55862, 55891, 55921, 55950, 55980, 56010, 56039, 56069, 56099, 56128, 56158, 56187, 56217, 56246, 56275, 56305, 56334, 56364, 56393, 56423, 56453, 56482, 56512, 56542, 56571, 56600, 56630, 56659, 56689, 56718, 56748, 56777, 56807, 56836, 56866, 56896, 56925, 56955, 56984, 57014, 57043, 57073, 57102, 57132, 57161, 57191, 57220, 57250, 57279, 57309, 57339, 57368, 57398, 57428, 57457, 57486, 57516, 57545, 57575, 57604, 57633, 57663, 57693, 57722, 57752, 57782, 57812, 57841, 57870, 57900, 57929, 57958, 57988, 58017, 58047, 58076, 58106, 58136, 58166, 58195, 58225, 58254, 58284, 58313, 58342, 58372, 58401, 58431, 58460, 58490, 58520, 58550, 58579, 58609, 58638, 58668, 58697, 58726, 58756, 58785, 58815, 58844, 58874, 58904, 58933, 58963, 58993, 59022, 59052, 59081, 59110, 59140, 59169, 59199, 59228, 59258, 59287, 59317, 59347, 59376, 59406, 59435, 59465, 59494, 59524, 59553, 59583, 59612, 59642, 59671, 59701, 59730, 59760, 59790, 59819, 59849, 59878, 59908, 59938, 59967, 59996, 60026, 60055, 60085, 60114, 60144, 60173, 60203, 60233, 60263, 60292, 60322, 60351, 60380, 60410, 60439, 60468, 60498, 60527, 60557, 60587, 60617, 60647, 60676, 60706, 60735, 60764, 60794, 60823, 60852, 60882, 60911, 60941, 60971, 61001, 61030, 61060, 61089, 61119, 61148, 61178, 61207, 61236, 61266, 61295, 61325, 61355, 61384, 61414, 61444, 61473, 61503, 61532, 61562, 61591, 61620, 61650, 61679, 61709, 61738, 61768, 61798, 61827, 61857, 61887, 61916, 61946, 61975, 62005, 62034, 62063, 62093, 62122, 62152, 62181, 62211, 62241, 62270, 62300, 62330, 62359, 62389, 62418, 62447, 62477, 62506, 62536, 62565, 62595, 62624, 62654, 62684, 62714, 62743, 62773, 62802, 62831, 62861, 62890, 62920, 62949, 62979, 63008, 63038, 63068, 63098, 63127, 63156, 63186, 63215, 63245, 63274, 63304, 63333, 63362, 63392, 63422, 63452, 63481, 63511, 63540, 63570, 63599, 63629, 63658, 63688, 63717, 63746, 63776, 63806, 63835, 63865, 63894, 63924, 63954, 63983, 64013, 64042, 64072, 64101, 64130, 64160, 64189, 64219, 64248, 64278, 64308, 64338, 64367, 64397, 64426, 64456, 64485, 64514, 64544, 64573, 64602, 64632, 64662, 64691, 64721, 64751, 64781, 64810, 64840, 64869, 64898, 64928, 64957, 64986, 65016, 65046, 65075, 65105, 65135, 65165, 65194, 65224, 65253, 65282, 65312, 65341, 65370, 65400, 65430, 65459, 65489, 65519, 65548, 65578, 65607, 65637, 65666, 65696, 65725, 65754, 65784, 65814, 65843, 65873, 65902, 65932, 65962, 65991, 66021, 66050, 66080, 66109, 66139, 66168, 66198, 66227, 66257, 66286, 66316, 66345, 66375, 66404, 66434, 66464, 66493, 66523, 66552, 66582, 66611, 66640, 66670, 66699, 66729, 66759, 66788, 66818, 66848, 66877, 66907, 66936, 66966, 66995, 67024, 67054, 67083, 67113, 67142, 67172, 67202, 67232, 67261, 67291, 67320, 67350, 67379, 67408, 67438, 67467, 67496, 67526, 67556, 67586, 67616, 67645, 67675, 67704, 67734, 67763, 67792, 67822, 67851, 67881, 67910, 67940, 67970, 67999, 68029, 68059, 68088, 68118, 68147, 68176, 68206, 68235, 68265, 68294, 68324, 68353, 68383, 68413, 68442, 68472, 68501, 68531, 68560, 68590, 68619, 68649, 68678, 68707, 68737, 68767, 68796, 68826, 68856, 68885, 68915, 68945, 68974, 69003, 69033, 69062, 69091, 69121, 69151, 69180, 69210, 69239, 69269, 69299, 69329, 69358, 69387, 69417, 69446, 69475, 69505, 69534, 69564, 69594, 69623, 69653, 69683, 69712, 69742, 69771, 69801, 69830, 69860, 69889, 69918, 69948, 69977, 70007, 70037, 70066, 70096, 70125, 70155, 70185, 70214, 70244, 70273, 70302, 70332, 70361, 70391, 70420, 70450, 70480, 70509, 70539, 70569, 70598, 70628, 70657, 70686, 70716, 70745, 70775, 70804, 70834, 70863, 70893, 70923, 70953, 70982, 71012, 71041, 71070, 71100, 71129, 71158, 71188, 71217, 71247, 71277, 71307, 71336, 71366, 71396, 71425, 71454, 71484, 71513, 71542, 71572, 71601, 71631, 71661, 71690, 71720, 71750, 71780, 71809, 71838, 71868, 71897, 71926, 71956, 71985, 72015, 72045, 72074, 72104, 72134, 72163, 72193, 72222, 72252, 72281, 72310, 72340, 72369, 72399, 72428, 72458, 72488, 72517, 72547, 72576, 72606, 72636, 72665, 72694, 72724, 72753, 72783, 72812, 72842, 72871, 72901, 72930, 72960, 72990, 73019, 73049, 73079, 73108, 73138, 73167, 73196, 73226, 73255, 73285, 73314, 73344, 73374, 73403, 73433, 73463, 73492, 73522, 73551, 73580, 73610, 73639, 73668, 73698, 73728, 73757, 73787, 73817, 73847, 73876, 73906, 73935, 73964, 73994, 74023, 74052, 74082, 74112, 74141, 74171, 74201, 74230, 74260, 74290, 74319, 74348, 74378, 74407, 74436, 74466, 74496, 74525, 74555, 74585, 74614, 74644, 74674, 74703, 74732, 74762, 74791, 74821, 74850, 74880, 74909, 74939, 74968, 74998, 75028, 75057, 75087, 75116, 75146, 75175, 75205, 75234, 75263, 75293, 75322, 75352, 75382, 75411, 75441, 75471, 75500, 75530, 75559, 75589, 75618, 75647, 75677, 75706, 75736, 75765, 75795, 75825, 75855, 75884, 75914, 75943, 75973, 76002, 76031, 76061, 76090, 76120, 76149, 76179, 76209, 76238, 76268, 76298, 76327, 76357, 76386, 76415, 76445, 76474, 76504, 76533, 76563, 76592, 76622, 76652, 76681, 76711, 76740, 76770, 76800, 76829, 76858, 76888, 76917, 76947, 76976, 77006, 77035, 77065, 77095, 77124, 77154, 77184, 77213, 77242, 77272, 77301, 77331, 77360, 77389, 77419, 77449, 77478, 77508, 77538, 77568, 77597, 77626, 77656, 77685, 77714, 77744, 77773, 77803, 77832, 77862, 77892, 77922, 77951, 77981, 78010, 78040, 78069, 78098, 78128, 78157, 78187, 78216, 78246, 78276, 78306, 78335, 78365, 78394, 78424, 78453, 78482, 78512, 78541, 78571, 78600, 78630, 78660, 78689, 78719, 78749, 78778, 78807, 78837, 78866, 78896, 78925, 78955, 78984, 79014, 79043, 79073, 79103, 79132, 79162, 79191, 79221, 79250, 79280, 79309, 79339, 79368, 79398, 79427, 79457, 79486, 79516, 79545, 79575, 79605, 79634, 79664, 79694, 79723, 79752, 79782, 79811, 79840, 79870, 79899, 79929, 79959);

    /**
     * HijriDateTime::Constructor
     *
     * Pass these parameteres when creating a new instance
     * of this Class, and they will be used as defaults.
     * e.g $obj = new HijriDateTime("ar");
     * To use system defaults pass null for english names
     * @param $lang String language - "ar" for Arabic months name
     */
    function __construct() {
    }

    public function init() {
    }
    
    /**
     * returns a string formatted according to the given format string using the given
     * integer timestamp or the current time if no timestamp is given. In other words, timestamp 
     * is optional and defaults to the value of time(). 
     * 
     * @param $format String date format (see http://php.net/manual/en/function.date.php)
     * @param $timestamp Integer time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
     * 
     * @return String Returns the Hijri date according to format and timestamp in Arabic/English
     *
     */
    function date($format, $timestamp=0) {
        if ($timestamp === 0) {
            $timestamp = time();
        }
        list ($d, $D, $j, $l, $S, $F, $m, $M, $n, $t, $L, $o, $Y, $y, $w, $a, $A, $H, $i, $s, $O) = explode("/", date("d/D/j/l/S/F/m/M/n/t/L/o/Y/y/w/a/A/H/i/s/O", $timestamp));
        extract($this->GeToHijr($d, $m, $Y));
        $j = $day;
        $t = $ml;
        $L = $ln;
        $d = sprintf("%02d", $day);
        $m = sprintf("%02d", $month);
        $n = $month;
        $Y = $year;
        $y = substr($year, 2);
        $S = substr($j, -1) == 1 ? "st" : (substr($j, -1) == 2 ? "nd" : (substr($j, -1) == 3 ? "rd" : "th"));
        $F = $n;
        $M = $n;


        $D = $w;
        $l = $w;
        $S = "";
        $a = $a;
        $A = $A;
        $r = "$D, $j $M $Y $H:$i:$s $O";
        $davars = array("d", "D", "j", "l", "S", "F", "m", "M", "n", "t", "L", "o", "Y", "y", "a", "A", "r");
        $myvars = array($d, "¢", $j, "£", "ç", "¥", $m, "©", $n, $t, $L, $Y, $Y, $y, "ï", "â", "®");
        $format = str_replace($davars, $myvars, $format);
        $date = date($format, $timestamp);
        $date = str_replace(array("¢", "£", "ç", "¥", "©", "ï", "â", "®"), array($D, $l, $S, $F, $M, $a, $A, $r), $date);
        return $date;
    }

    /**
     * Returns an array of  month, day, year, ln: which is "Islamic lunation number 
     * (Births of New Moons)", int: The length of current month.
     * 
     * @param $day Integer Gregorian day of the month
     * @param $month Integer Gregorian month number
     * @param $year Integer Gregorian year in full (1999)
     * @return Array Hijri date[int month, int day, int year, int ln, int ml]
     * @see Robert Gent method maker (http://www.phys.uu.nl/~vgent/islam/ummalqura.htm)
     */
    public function GeToHijr($day=20, $month=02, $year=1976) {
        $jd = GregorianToJD($month, $day, $year);
        $mjd = $jd - 2400000;
        foreach ($this->jdl as $i => $v)
            if ($v > ($mjd - 1))
                break;
        $iln = $i + 15588; // Islamic lunation number (Births of New Moons)
        $ii = floor(($i - 1) / 12);
        $year = 1300 + $ii; // year
        $month = $i - 12 * $ii; // month
        $day = $mjd - $this->jdl[$i - 1]; //day
        $ml = $this->jdl[$i] - $this->jdl[$i - 1]; // Month Length
        list ($_Date["month"], $_Date["day"], $_Date["year"], $_Date["ln"], $_Date["ml"]) = array($month, $day, $year, $iln, $ml);
        return ($_Date);
    }

    /**
     * Parse about any English textual datetime description into a Hijri format
     * if no format the date returned will be Hijri d-m-Y 
     * @param String $strDate English textual datetime
     * @param String $format The date format needed
     * @param String $sep separator
     * @return String Date in Hijri as formated or like d-m-Y 
     */
    public function strToHijri($strDate, $format= "d-m-Y") {
        if ($format == "d-m-Y") {
            list ($d, $m, $Y) = explode("/", date("d/m/Y", strtotime($strDate)));
            extract($this->GeToHijr($d, $m, $Y));
            $date = "$day-$month-$year";
        } else {
            $date = $this->date($format, strtotime($strDate));
        }
        return $date;
    }

}