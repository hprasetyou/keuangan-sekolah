<?php
namespace App\Model;

class Jurnal {
public $find;
public $limit='';

public $id_transaksi;
public $akun_debet;
public $akun_kredit;
public $nominal;

   public function __construct($connection){
      $this->db= $connection;
   }

   public function post(){
     $this->db->conf['db_name']=$this->dbname;

     //posting kredit;
     $this->db->insert('jurnal',array(
        'id_transaksi'    =>$this->id_transaksi,
        'akun'            =>$this->akun_kredit,
        'debet'           =>0,
        'kredit'          =>$this->nominal
     ));

     $this->db->insert('jurnal',array(
        'id_transaksi'    =>$this->id_transaksi,
        'akun'            =>$this->akun_debet,
        'debet'           =>$this->nominal,
        'kredit'          =>0
     ));


   }

   public function show(){
       $this->db->conf['db_name']=$this->dbname;
       $condition = '';
       if(isset($this->find['akun'])){
          $condition .= " akun ='".$this->find['akun']."' AND ";
      }
      else{}
       $condition .= " date_format(waktu,'%Y-%m-%d') between '".$this->find['tanggal_mulai']."' AND '".$this->find['tanggal_akhir']."' " ;
       return $this->db->execute("SELECT jenis_akun, uraian,date_format(waktu,'%Y-%m-%d') as tanggal,id_transaksi , nama_akun as akun, debet, kredit,pencatat FROM `jurnal`
       join tb_transaksi on jurnal.id_transaksi=tb_transaksi.id join akun on jurnal.akun=akun.id_akun where ".$condition."  ORDER BY waktu DESC ".$this->limit);
   }

    public function sum(){
      $this->db->conf['db_name']=$this->dbname;
      $condition= "akun ='".$this->find['akun']."' AND tanggal between '".$this->find['tanggal_mulai']."' AND '".$this->find['tanggal_akhir']."'" ;
      return $this->db->execute("SELECT (sum(debet)-sum(kredit)) as saldo FROM jurnal where ".$condition);

    }
    public function sum_per_jenis(){
      $this->db->conf['db_name']=$this->dbname;
      $condition= "jenis_akun ='".$this->find['jenis']."' AND tanggal between '".$this->find['tanggal_mulai']."' AND '".$this->find['tanggal_akhir']."'" ;
      return $this->db->execute("SELECT id_akun,nama_akun,jenis_akun, sum(kredit) as kredit,sum(debet) as debet,
      case
      when(sum(kredit)>sum(debet))
        then 'kredit'
        else
        'debet'
        end
        as 'posisi'

       FROM `jurnal` JOIN akun on jurnal.akun= akun.id_akun GROUP BY akun");
    }


}
