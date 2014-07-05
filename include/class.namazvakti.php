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
			
			if ($sehir == $ilce)
			{
				$yer_adi = $ulkeler['veri'][$ulke] . '<br>' . $sehirler['veri'][$sehir];
			} else {
				$ilceler = $this->ilceler($sehir);
				$yer_adi = $sehirler['veri'][$sehir] . '<br>' . $ilceler['veri'][$ilce];
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
	
} // Sınıf Bitti
