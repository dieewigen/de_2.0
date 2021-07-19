<?php
/**
 * Description of viewer
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de

 *
 */
//require 'det_userdata.inc.php';
require '../../inc/sv.inc.php';

ini_set('memory_limit', '512M');

class viewer {
    public  $id1,$id2,$id3,$id4,$id5/*, $id6,$id7,$id8,$id9,$id10 */;

    private $colors;
    
    function  __construct($id1, $id2=0, $id3=0, $id4=0, $id5=0/* , $id6=0, $id7=0, $id8=0, $id9=0, $id10=0 */) {
        $this->id1 = $id1;
        $this->id2 = $id2;
        $this->id3 = $id3;
        $this->id4 = $id4;
        $this->id5 = $id5;
/* 		$this->id6 = $id6;
		$this->id7 = $id7;
		$this->id8 = $id8;
		$this->id9 = $id9;
		$this->id10 = $id10; */
        $this->colors[$this->id1] = '#ff0000';
        $this->colors[$this->id2] = '#00ff00';
        $this->colors[$this->id3] = '#0000ff';
        $this->colors[$this->id4] = '#0f0f0f';
        $this->colors[$this->id5] = '#f0f0f0';
/* 		$this->colors[$this->id6] = '#00FFFF';
		$this->colors[$this->id7] = '#CC9900';
		$this->colors[$this->id8] = '#CC3399';
		$this->colors[$this->id9] = '#FF9900';
		$this->colors[$this->id10] = '#660000'; */
    }



    function getDayTableRow($date) {
    
    global $sv_servid;
    
        $d = dbExtend::getInstance()->get(
            'select distinct HOUR(time) as hour , userid'
            .' from gameserverlogdata'
            .' where time >= '.sqlescape($date.' 00:00:00')
            .' AND ( userid = '.sqlescape($this->id1).' or userid='.sqlescape($this->id2).' or userid='.sqlescape($this->id3).' 
			or userid='.sqlescape($this->id4).' or userid='.sqlescape($this->id5).')'
            .' and time <= '.sqlescape($date.' 23:59:59')
            .' and file != '.sqlescape('/chat.php')
            .' and file != '.sqlescape('/efta_chat.php')
            .' and file != '.sqlescape('/sou_chat.php')
            .' AND serverid='.$sv_servid
            .' group by HOUR(time), id;'
        );
        
        $ret = '<tr>';
        $ret .= '<td class="date">'.$date.'</td>';
        for($h=0;$h<=23;$h++) {
            $ret .= '<td onclick="loadHour(\''.$date.'\','.$h.')">';
            foreach($d as $u) {
                if($u->hour == $h) {
                    if($u->userid == $this->id1) $ret .= '<span style="color:'.$this->colors[$this->id1].';">'.$this->id1.'</span><br>';
                    if($u->userid == $this->id2) $ret .= '<span style="color:'.$this->colors[$this->id2].';">'.$this->id2.'</span><br>';
                    if($u->userid == $this->id3) $ret .= '<span style="color:'.$this->colors[$this->id3].';">'.$this->id3.'</span><br>';
                    if($u->userid == $this->id4) $ret .= '<span style="color:'.$this->colors[$this->id4].';">'.$this->id4.'</span><br>';
                    if($u->userid == $this->id5) $ret .= '<span style="color:'.$this->colors[$this->id5].';">'.$this->id5.'</span><br>';
/* 					if($u->id == $this->id6) $ret .= '<span style="color:'.$this->colors[$this->id6].';">'.$this->id6.'</span><br>';
					if($u->id == $this->id7) $ret .= '<span style="color:'.$this->colors[$this->id7].';">'.$this->id7.'</span><br>';
					if($u->id == $this->id8) $ret .= '<span style="color:'.$this->colors[$this->id8].';">'.$this->id8.'</span><br>';
					if($u->id == $this->id9) $ret .= '<span style="color:'.$this->colors[$this->id9].';">'.$this->id9.'</span><br>';
					if($u->id == $this->id10) $ret .= '<span style="color:'.$this->colors[$this->id10].';">'.$this->id10.'</span><br>'; */
                }
            }
            $ret .= '</td>';
        }
        $ret .= '</tr>';
        return $ret;


    }

    function getHourOverview($date,$h) {
    
    global $sv_servid;
    
        $d = dbExtend::getInstance()->get('select * from gameserverlogdata '
            .' where ( userid = '.sqlescape($this->id1).' or userid='.sqlescape($this->id2).' or userid='.sqlescape($this->id3).' 
			or userid='.sqlescape($this->id4).' or userid='.sqlescape($this->id5)./* ' or id='.sqlescape($this->id6).' 
			or id='.sqlescape($this->id7).' or id='.sqlescape($this->id8).' or id='.sqlescape($this->id9).' 
			or id='.sqlescape($this->id10). */')'
            .' and time >= '.sqlescape($date.' '.$h.':00:00')
            .' and time <= '.sqlescape($date.' '.$h.':59:59')
            .' and file != '.sqlescape('/chat.php')
            .' and file != '.sqlescape('/efta_chat.php')
            .' and file != '.sqlescape('/sou_chat.php')
            .' AND serverid='.$sv_servid
            .' order by time asc'
        );
        
       
        $ret = '<table class="jTPS"><thead><tr><th>Zeit</th>'
        .'<th>'.$this->id1.'</th><th>'.$this->id2.'</th><th>'.$this->id3.'</th><th>'.$this->id4.'</th><th>'.$this->id5.'</th>'
		.'</tr></thead><tbody>'."\r\n";
		
		/* $ret = '<table class="jTPS"><thead><tr><th>Zeit</th>'
        .'<th>'.$this->id1.'</th><th>'.$this->id2.'</th><th>'.$this->id3.'</th><th>'.$this->id4.'</th><th>'.$this->id5.'</th>
		<th>'.$this->id6.'</th><th>'.$this->id7.'</th><th>'.$this->id8.'</th><th>'.$this->id9.'</th><th>'.$this->id10.'</th>'
		.'</tr></thead><tbody>'."\r\n"; */
		
        $timeIndex = strtotime($date.' '.$h.':00:00')-60;
        $rowClNum = 0;
        foreach($d as $u) {
            if(!$lastZeit) $lastZeit = strtotime($u->time);
            $rowTime = strtotime($u->time);
            if($rowTime >= $timeIndex+60) { $rowClNum = abs($rowClNum-1); $timeIndex = $timeIndex+60; }
            $ret .=  '<tr class="row'.$rowClNum.'">';
            $ret .= '<td>'.$u->time . '&nbsp;&nbsp;&nbsp;('.(strtotime($u->time) - $lastZeit).' s)'.'</td>';
            $ret .= '<td>'.(($u->userid==$this->id1)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->file.'</span>':'').'</td>';
            $ret .= '<td>'.(($u->userid==$this->id2)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->file.'</span>':'').'</td>';
            $ret .= '<td>'.(($u->userid==$this->id3)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->file.'</span>':'').'</td>';
            $ret .= '<td>'.(($u->userid==$this->id4)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->file.'</span>':'').'</td>';
            $ret .= '<td>'.(($u->userid==$this->id5)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->file.'</span>':'').'</td>';/* 
			$ret .= '<td>'.(($u->id==$this->id6)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->Datei.'</span>':'').'</td>';
			$ret .= '<td>'.(($u->id==$this->id7)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->Datei.'</span>':'').'</td>';
			$ret .= '<td>'.(($u->id==$this->id8)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->Datei.'</span>':'').'</td>';
			$ret .= '<td>'.(($u->id==$this->id9)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->Datei.'</span>':'').'</td>';
			$ret .= '<td>'.(($u->id==$this->id10)?'<span class="tt" alt="<pre>'.print_r($u,1).'</pre>">'.$u->Datei.'</span>':'').'</td>'; */
            $ret .=  '</tr>'."\r";
            $lastZeit = strtotime($u->time);
        }
        $ret .= '</tbody>';
        $ret  .= '<tfoot><tr><td colspan="6">';
        //        $ret .= ' <div class="pagination"></div>';
        //        $ret .= ' <div class="paginationTitle">&nbsp;</div>';
        //        $ret .= ' <div class="selectPerPage"></div>';
        //        $ret .= ' <div class="status"></div>';
        $ret .= '</td></tr></tfoot>'."\r\n";
        $ret .= '</table>';
        return $ret;

    }


}

?>
