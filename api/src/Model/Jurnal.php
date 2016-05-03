<?php
namespace App\Model;

class Jurnal {
public $find;
public $limit='';

public $id_transaksi;
public $akun_debet;
public $akun_kredit;
public $nominal;

   public function __construct(){
     $this->db = new \App\Helper\Connection();
     $this->db->conf['db_name']="KSAAS_". \App\Helper\Auth::user_data()->user_group;
 }

   public function post(){


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
   public function saldo(){
     $sql="SELECT akun.nama_akun, akun.id_akun,akun.jenis_akun, case when sum(debet)-sum(kredit) > 0 then sum(debet)-sum(kredit)
      when sum(debet)-sum(kredit) is null then 0 else 0 end as debet,
       case when sum(kredit)-sum(debet) > 0 then sum(kredit)-sum(debet)
        when sum(kredit)-sum(debet) is null then 0 else 0 end as kredit
        FROM `jurnal` join akun on akun.id_akun= jurnal.akun
        WHERE akun='".$this->find['akun']."' ";
        return $this->db->execute($sql);

   }

   public function show(){

       $condition = '';
       if(isset($this->find['akun'])){
          $condition .= " akun ='".$this->find['akun']."' AND ";
      }
      else{}
       $condition .= " date_format(waktu,'%Y-%m-%d') between '".$this->find['tanggal_mulai']."' AND '".$this->find['tanggal_akhir']."' " ;
       return $this->db->execute("SELECT jenis_akun, uraian,date_format(waktu,'%Y-%m-%d') as tanggal,id_transaksi , nama_akun as akun, debet, kredit,pencatat FROM `jurnal`
       join tb_transaksi on jurnal.id_transaksi=tb_transaksi.id join akun on jurnal.akun=akun.id_akun where ".$condition."  ORDER BY waktu DESC ".$this->limit);
   }

    public function sum($id,$jenis){
      $cond = "";
      if($jenis=='saldo'){
          $cond=" and not uraian like '%penyesuaian%' ";
      }else if($jenis=='penyesuaian'){
        $cond=" and uraian like '%penyesuaian%' ";
      }else if($jenis=='rl'){
        $cond=" and jenis_akun in('p','b') ";
      }else if($jenis=='neraca'){
        $cond=" and jenis_akun not in('p','b') ";
      }
      return $this->db->execute("SELECT case when sum(kredit)-sum(debet) > 0 then sum(kredit)-sum(debet) else 0 end as kredit,
      case when sum(debet) - sum(kredit) > 0 then sum(debet) - sum(kredit) else 0 end as debet

       FROM `jurnal`  join tb_transaksi on jurnal.id_transaksi= tb_transaksi.id join akun on akun.id_akun = jurnal.akun
       where akun ='".$id."' ".$cond);

    }
    public function sum_per_jenis(){

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
