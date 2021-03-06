<?php 

class Kasubag extends CI_Controller{

	function __construct(){
		parent::__construct();		
		if($this->session->userdata('logged_in') == TRUE){
			if($this->session->userdata('Status') == 'admin'){
				redirect('admin/index');
			}else if($this->session->userdata('Status') == 'operator'){
			   redirect('operator/index');
			}else if ($this->session->userdata('Bagian') != 'null' && $this->session->userdata('Sub_Bagian')=='null'){
				
				redirect('kbagian/index');
			}else if ($this->session->userdata('Bagian') != 'null' && $this->session->userdata('Sub_Bagian') != 'null' && $this->session->userdata('Urusan') !='null' && $this->session->userdata('jabatan') == 'kepala'){
				redirect('kaur/index');
			}else if ($this->session->userdata('Bagian') != 'null' && $this->session->userdata('Sub_Bagian') != 'null' && $this->session->userdata('Urusan') !='null' && $this->session->userdata('jabatan') == 'staff'){
				redirect('staff/index');
			}
		} else {
			redirect('auth/logout');
		}			
		$this->load->helper('url');
		$this->load->model('m_kasubag');
	}

	function index(){
		$tahun = date("Y");
		$data['main_view'] = 'tatausaha/kasubag/dashboard_view';
		$data['pribadi'] = $this->m_kasubag->get_bulan_surat_pribadi_perbulan_tahun($tahun);
		$data['pegawai'] = $this->m_kasubag->get_count_pegawai();
		$data['periode'] = $this->m_kasubag->get_periode($tahun);
		$data['bgn'] = $this->m_kasubag->get_jum_surat_bagian($tahun);
		$data['surat_terendah'] = $this->m_kasubag->get_jum_surat_bagian_terendah($tahun);
		$data['jumsuratpribadipertahun'] = $this->m_kasubag->get_jum_surat_pribadi_pertahun($tahun);
		//$data['surattugas'] = $this->m_kasubag->get_jml_surat();
		//$data['surattugas'] = $this->m_kasubag->get_jml_pegawai();
		//$data['detail_st'] = $this->m_kasubag->get_aktif_person();   
		$this->load->view('template', $data);
	}

	function indeks(){
		$tahun = $this->input->post('tahun');
		$data['main_view'] = 'tatausaha/kasubag/dashboard_view';
		$data['pribadi'] = $this->m_kasubag->get_bulan_surat_pribadi_perbulan_tahun($tahun);
		$data['pegawai'] = $this->m_kasubag->get_count_pegawai();
		$data['periode'] = $this->m_kasubag->get_periode($tahun);
		$data['bgn'] = $this->m_kasubag->get_jum_surat_bagian($tahun);
		$data['surat_terendah'] = $this->m_kasubag->get_jum_surat_bagian_terendah($tahun);
		$data['jumsuratpribadipertahun'] = $this->m_kasubag->get_jum_surat_pribadi_pertahun($tahun);
		//$data['surattugas'] = $this->m_kasubag->get_jml_surat();
		//$data['surattugas'] = $this->m_kasubag->get_jml_pegawai();
		//$data['detail_st'] = $this->m_kasubag->get_aktif_person();   
		$this->load->view('template', $data);
	}

	function tampil_surat(){
		$data['main_view'] = 'tatausaha/kasubag/v_tampil';
		$data['surattugas'] = $this->m_kasubag->tampil_surat();   
		$this->load->view('template', $data);
	}

	//function detail_surat($Nomor_Surat){
		//$data['detail_st'] = $this->m_kasubag->detail_surat($Nomor_Surat);   
		//$this->load->view('v_detail_surat', $data);
	//}

	function detail_surat($Nomor_Surat){
		$data['main_view'] = 'tatausaha/kasubag/v_detail_surat';
		$data2['main_view'] = 'tatausaha/kasubag/v_buat_detail_surat';
		$page = $_SERVER['PHP_SELF'];
		$values=explode("/",$page); 
		$length=sizeof($values); 
		$url=$values[$length-4].'/'.$values[$length-3].'/'.$values[$length-2].'/'.$values[$length-1]; 
		$cek = $this->m_kasubag->cek_ketersediaan_detail($url)->num_rows();
		if($cek > 0){
			$data['detail_st'] = $this->m_kasubag->detail_surat($url);   
			$data['surattugas'] = $this->m_kasubag->get_no_surat($url);   
			$this->load->view('template', $data);
		}else{
			print "<script type=\"text/javascript\">alert('Detail Surat $url tidak tersedia, Klik OK untuk membuat detail surat ini');</script>";			 			 
			$data2['surattugas'] = $this->m_kasubag->get_no_surat($url);   
			$data2['pegawai'] = $this->m_kasubag->get_pegawai();
			$this->load->view('template', $data2);
		}
	}

	//mmembuat detail surat
	function detailsurat(){
		$page = $_SERVER['PHP_SELF'];
		$values=explode("/",$page); 
		$length=sizeof($values); 
		$url=$values[$length-4].'/'.$values[$length-3].'/'.$values[$length-2].'/'.$values[$length-1]; 
		$data['surattugas'] = $this->m_kasubag->get_no_surat($url);   
		$data['main_view'] = 'tatausaha/kasubag/v_buat_detail_surat';
		$this->load->view('template',$data);
	}

		// lihat list TU	
    function Pegawai(){
		$data['main_view'] = 'tatausaha/kasubag/v_pegawai';
		$data['pegawai'] = $this->m_kasubag->tampilPegawai();
		$this->load->view('template', $data);
	}
	function filter(){
		$urusan = $this->input->post('Urusan');
		
		if( $urusan != null){
			$data['main_view'] = 'tatausaha/kasubag/v_pegawai';
			$data['pegawai'] = $this->m_kasubag->tampilPegawai_Urusan($urusan);
			$this->load->view('template',$data);
		}else if ($urusan == null){
			$this->session->set_flashdata('notif','Subbagian atau Urusan harap diisi');
			redirect('Kasubag/Pegawai');
			
		}
	}
	function cari_surat(){
		$keyword = $_POST['keyword'];
		$this->load->model('m_kasubag');
		$data['surattugas'] = $this->m_kasubag->cari_surat_tugas($keyword);
		$this->load->view('tatausaha/kasubag/v_tampil', $data);
	}	

	public function cari(){
	$data['main_view'] = 'tatausaha/kasubag/v_pegawai';
	$keyword = $this->input->get('cari', TRUE); //mengambil nilai dari form input cari
	$data['pegawai'] = $this->m_kasubag->cari($keyword); //mencari data karyawan berdasarkan inputan
	$this->load->view('template', $data); //menampilkan data yang sudah dicari
	}	
	
	function Surat($NIP){
		$data['main_view'] = 'tatausaha/kasubag/v_surat';
		$data2['main_view'] = 'tatausaha/kasubag/v_pegawai';
		$page = $_SERVER['PHP_SELF'];
		$values=explode("/",$page); 
		$length=sizeof($values); 
		$url=$values[$length-1]; 
		$cek = $this->m_kasubag->cek_ketersediaan_surat($url)->num_rows();
		if($cek > 0){
			$data['detail_st'] = $this->m_kasubag->SuratTugas($url);
			$this->load->view('template',$data);
		}else{
			print "<script type=\"text/javascript\">alert('Pegawai dengan NIP $url tidak mempunyai surat tugas');</script>";			 			 
			
		$data2['main_view'] = 'tatausaha/kasubag/v_pegawai';
		$data2['pegawai'] = $this->m_kasubag->tampilPegawaiTU();
		$this->load->view('template',$data2);
		}
		}
		
	function PencarianSurat(){
		$data['main_view'] = 'tatausaha/kasubag/v_surat';
		$bln1 = $this->input->get('bln1');
		$bln2 = $this->input->get('bln2');
		$tahun = $this->input->get('tahun');
		$nip = $this->input->get('NIP');
		$data['detail_st'] = $this->m_kasubag->SuratTugs($bln1,$bln2,$tahun,$nip);
		$this->load->view('template',$data);
	}

		// rekap surat pegawai perbulan
		function rekapsurat($NIP){
			$data['main_view'] = 'tatausaha/kasubag/v_datasurat';
			$data['detail_st'] = $this->m_kasubag->surat_perbulan($NIP);
			$data['surat'] = $this->m_kasubag->surat_rekap_perbulan($NIP);
			$data['detail'] = $this->m_kasubag->rekap_surat_perbulan($NIP);
			$this->load->view('template',$data);
		}
		function detailrekapsuratform($NIP,$bulan){
			$data['main_view'] = 'tatausaha/kasubag/v_rekap_bulan_pegawai';
			$data['detail_st'] = $this->m_kasubag->surat_perbulan_detail_form($NIP,$bulan);
			$data['surattugas'] = $this->m_kasubag->surat_perbulan_detail($NIP,$bulan);
			$this->load->view('template',$data);
		
		}
	
		function excelsuratpegawaiperbulan($nip,$bulan){
			$data['detail_st'] = $this->m_kasubag->surat_perbulan_detail_form($nip,$bulan);
			$data['surattugas'] = $this->m_kasubag->surat_perbulan_detail($nip,$bulan);
			$data['surattugas'] = $this->m_kasubag->tampil_surat();   
			$this->load->view('admin/export/v_surat_tugas_pegawai_perbulan', $data);
		}
	

	function data_admin(){
		$data['main_view'] = 'tatausaha/kasubag/v_data_admin';
		$data['user'] = $this->m_kasubag->getDataAdmin();   
		$this->load->view('template', $data);
	}

function rekap(){	  
		$nip= $this->session->userdata('Sub_Bagian');
		$str ="SELECT
		x.NIP,
		x.Nama,
		SUM(x.januari) AS januari,
		SUM(x.februari) AS februari,
		SUM(x.maret) AS maret,
		SUM(x.april) AS april,
		SUM(x.mei) AS mei,
		SUM(x.juni) AS juni,
		SUM(x.juli) AS juli,
		SUM(x.agustus) AS agustus,
		SUM(x.september) AS september,
		SUM(x.oktober) AS oktober,
		SUM(x.november) AS november,
		SUM(x.desember) AS desember,
		SUM(x.JumlahST) AS JumlahST
		FROM (
		SELECT 
		pegawai.NIP,
		pegawai.Nama,
		IF( MONTH(surattugas.Tanggal_Surat) = 1, COUNT(detail_st.NIP), 0)  AS januari,
		IF( MONTH(surattugas.Tanggal_Surat) = 2, COUNT(detail_st.NIP), 0)  AS februari,
		IF( MONTH(surattugas.Tanggal_Surat) = 3, COUNT(detail_st.NIP), 0)  AS maret,
		IF( MONTH(surattugas.Tanggal_Surat) = 4, COUNT(detail_st.NIP), 0)  AS april,
		IF( MONTH(surattugas.Tanggal_Surat) = 5, COUNT(detail_st.NIP), 0)  AS mei,
		IF( MONTH(surattugas.Tanggal_Surat) = 6, COUNT(detail_st.NIP), 0)  AS juni,
		IF( MONTH(surattugas.Tanggal_Surat) = 7, COUNT(detail_st.NIP), 0)  AS juli,
		IF( MONTH(surattugas.Tanggal_Surat) = 8, COUNT(detail_st.NIP), 0)  AS agustus,
		IF( MONTH(surattugas.Tanggal_Surat) = 9, COUNT(detail_st.NIP), 0)  AS september,
		IF( MONTH(surattugas.Tanggal_Surat) = 10, COUNT(detail_st.NIP), 0)  AS oktober,
		IF( MONTH(surattugas.Tanggal_Surat) = 11, COUNT(detail_st.NIP), 0)  AS november,
		IF( MONTH(surattugas.Tanggal_Surat) = 12, COUNT(detail_st.NIP), 0) AS desember,
		Count(detail_st.NIP) AS JumlahST
		FROM
		detail_st ,
		pegawai, surattugas
		WHERE
		detail_st.NIP = pegawai.NIP and detail_st.Nomor_Surat = surattugas.Nomor_Surat AND YEAR(surattugas.Tanggal_Surat) AND pegawai.Sub_Bagian = '";
		$st = "' GROUP BY
		pegawai.NIP,
		pegawai.Nama,
		MONTH (surattugas.Tanggal_Surat)) x
		GROUP BY x.NIP, x.Nama";
		$pegawai = $this->db->query($str.$nip.$st);
		$data = array();
		$data['main_view'] ="tatausaha/kasubag/v_rekap";
		$data['records'] = $pegawai->result_array();
		$this->load->view('template',$data);
	}
	function Pencarian(){
		//$nip= $this->session->userdata('Sub_Bagian');
		$username = $this->input->post('NIP');
		$tgl = $this->input->post('Tanggal_Surat');
		if($tgl == null && $username !=null) {
		$str1="SELECT
			x.NIP,
			x.Nama,
			SUM(x.januari) AS januari,
			SUM(x.februari) AS februari,
			SUM(x.maret) AS maret,
			SUM(x.april) AS april,
			SUM(x.mei) AS mei,
			SUM(x.juni) AS juni,
			SUM(x.juli) AS juli,
			SUM(x.agustus) AS agustus,
			SUM(x.september) AS september,
			SUM(x.oktober) AS oktober,
			SUM(x.november) AS november,
			SUM(x.desember) AS desember,
			SUM(x.JumlahST) AS JumlahST ";
			$str3=" GROUP BY x.NIP, x.Nama";
			$str = "SELECT
			detail_st.NIP,
			pegawai.Nama,
			IF( MONTH(surattugas.Tanggal_Surat) = 1, COUNT(detail_st.NIP), 0)  AS januari,
			IF( MONTH(surattugas.Tanggal_Surat) = 2, COUNT(detail_st.NIP), 0)  AS februari,
			IF( MONTH(surattugas.Tanggal_Surat) = 3, COUNT(detail_st.NIP), 0)  AS maret,
			IF( MONTH(surattugas.Tanggal_Surat) = 4, COUNT(detail_st.NIP), 0)  AS april,
			IF( MONTH(surattugas.Tanggal_Surat) = 5, COUNT(detail_st.NIP), 0)  AS mei,
			IF( MONTH(surattugas.Tanggal_Surat) = 6, COUNT(detail_st.NIP), 0)  AS juni,
			IF( MONTH(surattugas.Tanggal_Surat) = 7, COUNT(detail_st.NIP), 0)  AS juli,
			IF( MONTH(surattugas.Tanggal_Surat) = 8, COUNT(detail_st.NIP), 0)  AS agustus,
			IF( MONTH(surattugas.Tanggal_Surat) = 9, COUNT(detail_st.NIP), 0)  AS september,
			IF( MONTH(surattugas.Tanggal_Surat) = 10, COUNT(detail_st.NIP), 0)  AS oktober,
			IF( MONTH(surattugas.Tanggal_Surat) = 11, COUNT(detail_st.NIP), 0)  AS november,
			IF( MONTH(surattugas.Tanggal_Surat) = 12, COUNT(detail_st.NIP), 0) AS desember,
			Count(detail_st.NIP) AS JumlahST
			FROM
			detail_st ,
			pegawai, surattugas
			WHERE
			detail_st.NIP = pegawai.NIP and detail_st.Nomor_Surat = surattugas.Nomor_Surat";
			$st = " GROUP BY pegawai.NIP, pegawai.Nama, MONTH(surattugas.Tanggal_Surat) ";
			$pegawai = $this->db->query($str1."FROM (".$str.$tgl.$st.") x".$str3);
			$data = array();
			$data['records'] = $pegawai->result_array();
			$data['pegawai'] = $pegawai->result_array();
			$this->session->set_flashdata('msg', 
						'<div class="alert alert-danger">
							<h4>Oppss</h4>
							<p>Tahun belum di input '.$tgl.'</p>
						</div>');    
						$data['main_view'] = "tatausaha/kasubag/v_rekap";
				$this->load->view('template',$data);      
			} else if ($username == null && $tgl != null) {
			$str1="SELECT
			x.NIP,
			x.Nama,
			SUM(x.januari) AS januari,
			SUM(x.februari) AS februari,
			SUM(x.maret) AS maret,
			SUM(x.april) AS april,
			SUM(x.mei) AS mei,
			SUM(x.juni) AS juni,
			SUM(x.juli) AS juli,
			SUM(x.agustus) AS agustus,
			SUM(x.september) AS september,
			SUM(x.oktober) AS oktober,
			SUM(x.november) AS november,
			SUM(x.desember) AS desember,
			SUM(x.JumlahST) AS JumlahST ";
			$str3=" GROUP BY x.NIP, x.Nama";
			$str = "SELECT
			detail_st.NIP,
			pegawai.Nama,
			IF( MONTH(surattugas.Tanggal_Surat) = 1, COUNT(detail_st.NIP), 0)  AS januari,
			IF( MONTH(surattugas.Tanggal_Surat) = 2, COUNT(detail_st.NIP), 0)  AS februari,
			IF( MONTH(surattugas.Tanggal_Surat) = 3, COUNT(detail_st.NIP), 0)  AS maret,
			IF( MONTH(surattugas.Tanggal_Surat) = 4, COUNT(detail_st.NIP), 0)  AS april,
			IF( MONTH(surattugas.Tanggal_Surat) = 5, COUNT(detail_st.NIP), 0)  AS mei,
			IF( MONTH(surattugas.Tanggal_Surat) = 6, COUNT(detail_st.NIP), 0)  AS juni,
			IF( MONTH(surattugas.Tanggal_Surat) = 7, COUNT(detail_st.NIP), 0)  AS juli,
			IF( MONTH(surattugas.Tanggal_Surat) = 8, COUNT(detail_st.NIP), 0)  AS agustus,
			IF( MONTH(surattugas.Tanggal_Surat) = 9, COUNT(detail_st.NIP), 0)  AS september,
			IF( MONTH(surattugas.Tanggal_Surat) = 10, COUNT(detail_st.NIP), 0)  AS oktober,
			IF( MONTH(surattugas.Tanggal_Surat) = 11, COUNT(detail_st.NIP), 0)  AS november,
			IF( MONTH(surattugas.Tanggal_Surat) = 12, COUNT(detail_st.NIP), 0) AS desember,
			Count(detail_st.NIP) AS JumlahST
			FROM
			detail_st ,
			pegawai, surattugas
			WHERE
			detail_st.NIP = pegawai.NIP and detail_st.Nomor_Surat = surattugas.Nomor_Surat AND YEAR(surattugas.Tanggal_Surat) =  ";
			$st = " GROUP BY pegawai.NIP, pegawai.Nama, MONTH(surattugas.Tanggal_Surat) ";
			$pegawai = $this->db->query($str1."FROM (".$str.$tgl.$st.") x".$str3);
			$data = array();
			$data['records'] = $pegawai->result_array();
			$this->session->set_flashdata('msg', 
						'<div class="alert alert-success">
							<p>Tahun berhasil di input '.$tgl.'</p>
						</div>');    
						$data['main_view'] = "tatausaha/kasubag/v_rekap";
				$this->load->view('template',$data);
			}else if($tgl == null && $username ==null){
			$str1="SELECT
			x.NIP,
			x.Nama,
			SUM(x.januari) AS januari,
			SUM(x.februari) AS februari,
			SUM(x.maret) AS maret,
			SUM(x.april) AS april,
			SUM(x.mei) AS mei,
			SUM(x.juni) AS juni,
			SUM(x.juli) AS juli,
			SUM(x.agustus) AS agustus,
			SUM(x.september) AS september,
			SUM(x.oktober) AS oktober,
			SUM(x.november) AS november,
			SUM(x.desember) AS desember,
			SUM(x.JumlahST) AS JumlahST ";
			$str3=" GROUP BY x.NIP, x.Nama";
			$str = "SELECT
			detail_st.NIP,
			pegawai.Nama,
			IF( MONTH(surattugas.Tanggal_Surat) = 1, COUNT(detail_st.NIP), 0)  AS januari,
			IF( MONTH(surattugas.Tanggal_Surat) = 2, COUNT(detail_st.NIP), 0)  AS februari,
			IF( MONTH(surattugas.Tanggal_Surat) = 3, COUNT(detail_st.NIP), 0)  AS maret,
			IF( MONTH(surattugas.Tanggal_Surat) = 4, COUNT(detail_st.NIP), 0)  AS april,
			IF( MONTH(surattugas.Tanggal_Surat) = 5, COUNT(detail_st.NIP), 0)  AS mei,
			IF( MONTH(surattugas.Tanggal_Surat) = 6, COUNT(detail_st.NIP), 0)  AS juni,
			IF( MONTH(surattugas.Tanggal_Surat) = 7, COUNT(detail_st.NIP), 0)  AS juli,
			IF( MONTH(surattugas.Tanggal_Surat) = 8, COUNT(detail_st.NIP), 0)  AS agustus,
			IF( MONTH(surattugas.Tanggal_Surat) = 9, COUNT(detail_st.NIP), 0)  AS september,
			IF( MONTH(surattugas.Tanggal_Surat) = 10, COUNT(detail_st.NIP), 0)  AS oktober,
			IF( MONTH(surattugas.Tanggal_Surat) = 11, COUNT(detail_st.NIP), 0)  AS november,
			IF( MONTH(surattugas.Tanggal_Surat) = 12, COUNT(detail_st.NIP), 0) AS desember,
			Count(detail_st.NIP) AS JumlahST
			FROM
			detail_st ,
			pegawai, surattugas
			WHERE
			detail_st.NIP = pegawai.NIP and detail_st.Nomor_Surat = surattugas.Nomor_Surat ";
			$st = " GROUP BY pegawai.NIP, pegawai.Nama, MONTH(surattugas.Tanggal_Surat) ";
			$pegawai = $this->db->query($str1."FROM (".$str.$st.") x  ".$str3);
			$data = array();
			$data['records'] = $pegawai->result_array();
			$this->session->set_flashdata('msg', 
						'<div class="alert alert-danger">
							<h4>Oppss</h4>
							<p>Tahun dan NIP belum di input </p>
						</div>');    
						$data['main_view'] = "tatausaha/kasubag/v_rekap";
				$this->load->view('template',$data);
			}else{
			$str1="SELECT
			x.NIP,
			x.Nama,
			SUM(x.januari) AS januari,
			SUM(x.februari) AS februari,
			SUM(x.maret) AS maret,
			SUM(x.april) AS april,
			SUM(x.mei) AS mei,
			SUM(x.juni) AS juni,
			SUM(x.juli) AS juli,
			SUM(x.agustus) AS agustus,
			SUM(x.september) AS september,
			SUM(x.oktober) AS oktober,
			SUM(x.november) AS november,
			SUM(x.desember) AS desember,
			SUM(x.JumlahST) AS JumlahST ";
			$str3=" GROUP BY x.NIP, x.Nama";
			$str = "SELECT
			detail_st.NIP,
			pegawai.Nama,
			IF( MONTH(surattugas.Tanggal_Surat) = 1, COUNT(detail_st.NIP), 0)  AS januari,
			IF( MONTH(surattugas.Tanggal_Surat) = 2, COUNT(detail_st.NIP), 0)  AS februari,
			IF( MONTH(surattugas.Tanggal_Surat) = 3, COUNT(detail_st.NIP), 0)  AS maret,
			IF( MONTH(surattugas.Tanggal_Surat) = 4, COUNT(detail_st.NIP), 0)  AS april,
			IF( MONTH(surattugas.Tanggal_Surat) = 5, COUNT(detail_st.NIP), 0)  AS mei,
			IF( MONTH(surattugas.Tanggal_Surat) = 6, COUNT(detail_st.NIP), 0)  AS juni,
			IF( MONTH(surattugas.Tanggal_Surat) = 7, COUNT(detail_st.NIP), 0)  AS juli,
			IF( MONTH(surattugas.Tanggal_Surat) = 8, COUNT(detail_st.NIP), 0)  AS agustus,
			IF( MONTH(surattugas.Tanggal_Surat) = 9, COUNT(detail_st.NIP), 0)  AS september,
			IF( MONTH(surattugas.Tanggal_Surat) = 10, COUNT(detail_st.NIP), 0)  AS oktober,
			IF( MONTH(surattugas.Tanggal_Surat) = 11, COUNT(detail_st.NIP), 0)  AS november,
			IF( MONTH(surattugas.Tanggal_Surat) = 12, COUNT(detail_st.NIP), 0) AS desember,
			Count(detail_st.NIP) AS JumlahST
			FROM
			detail_st ,
			pegawai, surattugas
			WHERE
			detail_st.NIP = pegawai.NIP and detail_st.Nomor_Surat = surattugas.Nomor_Surat AND YEAR(surattugas.Tanggal_Surat) =  ";
			$st = " GROUP BY pegawai.NIP, pegawai.Nama, MONTH(surattugas.Tanggal_Surat) ";
			$pegawai = $this->db->query($str1."FROM (".$str.$tgl." AND detail_st.NIP =  " .$username.$st.") x".$str3);
			$data = array();
			$data['records'] = $pegawai->result_array();
			$data['pegawai'] = $pegawai->result_array();
			$this->session->set_flashdata('msg', 
						'<div class="alert alert-success">
							<h4>Oppss</h4>
							<p>Tahun dan NIP berhasil  di input </p>
						</div>');
						$data['main_view'] = "tatausaha/kasubag/v_rekap";
			$this->load->view('template',$data);		
		}
	}
		
	} 
?>