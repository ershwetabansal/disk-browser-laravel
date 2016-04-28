{if member_group == "1" OR member_group == "6"}

<?php $offset = -0; ?>

<style type="text/css">

    @font-face {font-family: 'Open Sans Regular';font-style: normal;font-weight: 400;src: local('Open Sans Regular'), local('OpenSans-Regular'), url(//www.energyaspects.com/lib/fonts/opensansregular.woff) format('woff');}

    body{
        width:100%;
        background-color:#f0f0f0;
        font-family:"Open Sans Regular", "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size:16px;
        line-height:1.5em;
        color:#333;
        clear:both;
        margin:0;
        padding:0
    }

    a {
        color:#333;
        text-decoration: none;

    }

    #admintbl td {
        vertical-align: middle;
        border-bottom: 1px solid #999;
        padding: 5px;
    }
    #admintbl {
        width: 100%;
    }
    .unit{
        width: 100%;
        height: 32%;
        background-color:#fff;
        vertical-align: top;
        display: inline-block;
        margin:7px;
        border: 1px solid #999;
        padding: 0;
    }
    .liveheader{
        width: 100%;
        height: 50px;
        vertical-align: middle;
        display: inline-block;
        margin: 0;
        border-bottom: 1px solid #999;
        background-color: #006699;
        color: #fff;
        padding:0;
        font-size: 200%;
    }
    .type {
        text-transform: uppercase;
        color: #000;
        padding-left: 3px;
        padding-right: 3px;
        width: 120px;
        margin-left: 5px;
        margin-right: 5px;
        font-size: 90%;
        display: inline-block;

    }
    .type_text {

        display: inline-block;
        width: 45%;
    }
    .type_time{

        display: inline-block;
        font-size: 90%;

    }
    .recentcount {
        text-align:center;
        vertical-align:middle;
        width:35px;
        display:inline-block;
        margin-left:5px;
        margin-bottom:5px;
        padding:2px;
        height: 30px;
    }
    .recentcountwide {
        text-align:center;
        vertical-align:middle;
        width:90px;
        display:inline-block;
        margin-left:5px;
        margin-bottom:5px;
        padding:2px;
        min-height: 30px;
    }

</style>





<?php

echo "<div class='unit' style='width:45%; height:98%; background-color:#fff;'><span class='liveheader'><span style='display:inline-block;padding-left:10px;padding-top:10px;vertical-align:middle;'>";

echo "Recent publications";
echo "</span></span>";

echo "<div class='' style='background-color: #ccc;display:inline-block;margin:5px;padding:2px;'>Scheduled</div>";
echo "<div class='' style='background-color: #666;color:#fff;display:inline-block;margin:5px;padding:2px;'>Sent</div>";
echo "<div class='' style='background-color: #CBE0A9;display:inline-block;margin:5px;padding:2px;'>Opened e-mail (unique)</div>";
echo "<div class='' style='background-color: #E6FFC3;display:inline-block;margin:5px;padding:2px;'>Downloads (unique)</div>";


$results = $this->EE->db->query("
				SELECT id, entryid, subject, emailType FROM exp_email_text WHERE hide!='1' AND (sendStatus='Sent' OR sendStatus IS NULL) ORDER BY id DESC LIMIT 11
				");


// MODIFICATION

foreach($results->result_array() as $recent){

    if($recent['emailType']=='PDF Report'){


        echo "<div style='margin:5px; vertical-align:middle; border-bottom:1px solid #ccc;min-height:50px;'>";
        echo "<div style='display:inline-block;width:300px; vertical-align:middle; min-height:50px;'><a href='https://www.energyaspects.com/email-pdf-reports/edit/".$recent['id']."?returnpage=recipients'>";
        $subjtext = str_replace(" | ","<br><i>",$recent['subject']);
        if(strlen($subjtext )>73){
            echo substr($subjtext ,0,70)."...";
        } else {
            echo $subjtext;
        }

        echo "</a></i></div>";
        echo "<div style='display:inline-block;'>";
        $attachs = $this->EE->db->query("SELECT item_text FROM exp_log_all WHERE email_id='".$recent['id']."' AND action='report_download' AND ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl) GROUP BY item_text");


        $opens = $this->EE->db->query("SELECT COUNT(*) as opens, COUNT(DISTINCT user_id) as uniqueopens FROM exp_log_all WHERE email_id='".$recent['id']."' AND action='email_open' AND ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl)");
        $allrecipients = $this->EE->db->query("SELECT COUNT(subid) as allrecipients FROM exp_email_recipients WHERE emailid='".$recent['id']."'");
        $sent = $this->EE->db->query("SELECT COUNT(subid) as sent FROM exp_email_recipients WHERE emailid='".$recent['id']."' AND issent='1'");
        $pending = $this->EE->db->query("SELECT COUNT(subid) as pending FROM exp_email_recipients WHERE emailid='".$recent['id']."' AND issent='0'");
        echo "<div class='recentcount' style='background-color: #ccc;'>".$allrecipients->row('allrecipients')."</div>";
        echo "<div class='recentcount' style='background-color: #666;color:#fff;'>".$sent->row('sent')."</div>";
        echo "<div class='recentcountwide' style='background-color: #CBE0A9;'>";
        if ($opens->row('opens')==0){ echo "-</div>";} else{ echo $opens->row('opens')." (".$opens->row('uniqueopens').")</div>";}
        echo "<div class='recentcountwide' style='background-color: #E6FFC3;'>";
        $i = 0;
        foreach($attachs->result_array() as $attachm){
            if($i>0)echo "<br>";

            $downloads = $this->EE->db->query("SELECT COUNT(*) as downloads, COUNT(DISTINCT user_id) as uniquedownloads FROM exp_log_all WHERE item_text='".$attachm['item_text']."' AND email_id='".$recent['id']."' AND action='report_download' AND ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl)");

            if ($downloads->row('downloads')==0){ echo "-";} else{ echo $downloads->row('downloads')." (".$downloads->row('uniquedownloads').")";}
            $i++;

        }
        echo "</div>";


        echo "</div>";
        echo "</div>";


    } else {

        $scheduled = $this->EE->db->query("SELECT count(id) as cnt FROM exp_email_recipients WHERE emailid = '".$recent['id']."'");
        $issent = $this->EE->db->query("SELECT count(id) as cnt FROM exp_email_recipients WHERE emailid = '".$recent['id']."' AND issent='1'");

        $unique = $this->EE->db->query("
			SELECT DATE_FORMAT( FROM_UNIXTIME( c.entry_date ) ,  '%e %b %Y' ) AS entrydate, c.title AS title, COUNT( a.member_id ) AS cnt, COUNT( DISTINCT (
			a.member_id
			) ) AS cntunique
			FROM exp_member_data AS a, exp_log_all AS b, exp_channel_titles AS c
			WHERE c.entry_id =  '".$recent['entryid']."'
			AND a.member_id = b.user_id
			AND b.entry_id = c.entry_id
			AND b.action='report_download'
			AND b.ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl)
			");

        $opened = $this->EE->db->query("SELECT count(id) as cnt, count(distinct(user_id)) as cntdistinct FROM exp_log_all WHERE action = 'email_open' AND ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl) AND email_id = '".$recent['id']."'");
        $downloads = $this->EE->db->query("SELECT count(id) as cnt FROM exp_log_all WHERE action = 'report_download' AND ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl) AND entry_id = '".$recent['entryid']."'");

        echo "<div style='margin:5px; vertical-align:middle; border-bottom:1px solid #ccc;height:50px;'>";
        echo "<div style='display:inline-block;width:300px; vertical-align:middle; height:50px;'><a href='https://www.energyaspects.com/email-pdf-reports/edit/".$recent['id']."?returnpage=recipients'>";
        $subjtext = str_replace(" | ","<br><i>",$recent['subject']);
        if(strlen($subjtext )>73){
            echo substr($subjtext ,0,70)."...";
        } else {
            echo $subjtext;
        }

        echo "</a></i></div>";
        echo "<div style='display:inline-block;'>";
        echo "<div class='recentcount' style='background-color: #ccc;'>".$scheduled->row('cnt')."</div>";
        echo "<div class='recentcount' style='background-color: #666;color:#fff;'>".$issent->row('cnt')."</div>";
        echo "<div class='recentcountwide' style='background-color: #CBE0A9;'>";
        if ($opened->row('cnt')==0){ echo "-</div>";} else{ echo $opened->row('cnt')." (".$opened->row('cntdistinct').")</div>";}
        echo "<div class='recentcountwide' style='background-color: #E6FFC3;'>";
        if ($downloads->row('cnt')==0){ echo "-</div>";} else{ echo $downloads->row('cnt')." (".$unique->row('cntunique').")</div>";}
        echo "</div>";

        echo "</div>";

    }


}
// END MODIFICATION


/*

	foreach($results->result_array() as $recent){

		$scheduled = $this->EE->db->query("SELECT count(id) as cnt FROM exp_email_recipients WHERE emailid = '".$recent['id']."'");
		$issent = $this->EE->db->query("SELECT count(id) as cnt FROM exp_email_recipients WHERE emailid = '".$recent['id']."' AND issent='1'");

		$unique = $this->EE->db->query("
		SELECT DATE_FORMAT( FROM_UNIXTIME( c.entry_date ) ,  '%e %b %Y' ) AS entrydate, c.title AS title, COUNT( a.member_id ) AS cnt, COUNT( DISTINCT (
		a.member_id
		) ) AS cntunique
		FROM exp_member_data AS a, exp_log_all AS b, exp_channel_titles AS c
		WHERE c.entry_id =  '".$recent['entryid']."'
		AND a.member_id = b.user_id
		AND b.entry_id = c.entry_id
		AND b.action='report_download'
		AND b.ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl)
		");

		$opened = $this->EE->db->query("SELECT count(id) as cnt, count(distinct(user_id)) as cntdistinct FROM exp_log_all WHERE action = 'email_open' AND ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl) AND email_id = '".$recent['id']."'");
		$downloads = $this->EE->db->query("SELECT count(id) as cnt FROM exp_log_all WHERE action = 'report_download' AND ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl) AND entry_id = '".$recent['entryid']."'");

		echo "<div style='margin:5px; vertical-align:middle; border-bottom:1px solid #ccc;height:50px;'>";
			echo "<div style='display:inline-block;width:300px; vertical-align:middle; height:50px;'><a href='https://www.energyaspects.com/email-pdf-reports/edit/".$recent['id']."?returnpage=recipients'>";
				$subjtext = str_replace(" | ","<br><i>",$recent['subject']);
				if(strlen($subjtext )>75){
					echo substr($subjtext ,0,72)."...";
				} else {
					echo $subjtext;
				}

			echo "</a></i></div>";
			echo "<div style='display:inline-block;'>";
				echo "<div class='recentcount' style='background-color: #ccc;'>".$scheduled->row('cnt')."</div>";
				echo "<div class='recentcount' style='background-color: #666;color:#fff;'>".$issent->row('cnt')."</div>";
				echo "<div class='recentcountwide' style='background-color: #CBE0A9;'>";
				if ($opened->row('cnt')==0){ echo "-</div>";} else{ echo $opened->row('cnt')." (".$opened->row('cntdistinct').")</div>";}
				echo "<div class='recentcountwide' style='background-color: #E6FFC3;'>";
				if ($downloads->row('cnt')==0){ echo "-</div>";} else{ echo $downloads->row('cnt')." (".$unique->row('cntunique').")</div>";}


			echo "</div>";
		echo "</div>";
	}

	*/

echo "</div><div id='wrapper' style='display:inline-block;width:53%;'>";



$results = $this->EE->db->query("
				SELECT
					a.ipaddr,
					a.browser,
					a.user_id,
					a.item_text,
					concat(d.m_field_id_4,' ',d.m_field_id_5) as user_name,
					d.m_field_id_6 as user_company
				FROM
					(SELECT id, ipaddr,item_text,browser,user_id FROM exp_log_all ORDER BY id DESC LIMIT 1000) as a
					LEFT OUTER JOIN exp_member_data AS d ON a.user_id = d.member_id
				WHERE
					a.browser NOT LIKE '%Googlebot%'
					AND a.browser NOT LIKE '%bot%'
					AND a.browser NOT LIKE '%Yammybot%'
					AND a.browser NOT LIKE '%Openbot%'
					AND a.browser NOT LIKE '%Yahoo%'
					AND a.browser NOT LIKE '%Baiduspider%'
					AND a.browser NOT LIKE '%Pingdom%'
					AND a.browser NOT LIKE '%YandexBot%'
					AND a.browser NOT LIKE '%CloudFront%'
					AND a.browser NOT LIKE '%NewRelicPinger%'
					AND a.browser NOT LIKE '%bingbot%'
					AND a.browser NOT LIKE '%Slurp%'
					AND a.browser NOT LIKE '%msnbot%'
					AND a.browser NOT LIKE '%ia_archiver%'
					AND a.browser NOT LIKE '%Lycos%'
					AND a.browser NOT LIKE '%Scooter%'
					AND a.browser NOT LIKE '%AltaVista%'
					AND a.browser NOT LIKE '%Googlebot%'
					AND a.browser NOT LIKE '%360spider%'
					AND a.item_text != 'autodiscover/autodiscover.xml'
					AND a.ipaddr NOT IN (SELECT ipaddr FROM exp_log_ipexcl)

				GROUP BY
					a.ipaddr,a.user_id
				ORDER BY
					a.id DESC, a.user_id DESC
				LIMIT 3
				");






foreach($results->result_array() as $ipaddr){

    echo "<div class='unit'><span class='liveheader'><span style='display:inline-block;padding-left:10px;padding-top:10px;vertical-align:middle;'>";

    if($ipaddr['user_id'] != 0){
        $name = $ipaddr['user_name']."</a> at ".$ipaddr['user_company'];
        if(strlen($name) > 45) $name = substr($name,0,42)."...";
        echo "".$name."</span></span>";
        echo "<br><span style='margin-left:5px;'> " . $ipaddr['ipaddr'] . ", ". substr($ipaddr['browser'],0,60) . "...</span>";
    } else {
        $results3 = $this->EE->db->query("
						SELECT
							d.m_field_id_6 as user_company
						FROM
							exp_log_all AS a
							LEFT OUTER JOIN exp_member_data AS d ON a.user_id = d.member_id
						WHERE
							a.ipaddr = '".$ipaddr['ipaddr']."'
							AND a.user_id != '0'
						GROUP BY user_id
						ORDER BY
							count(id) DESC
						LIMIT 1

			");

        if ($results3->num_rows() > 0 && $results3->row('user_company') != ''){
            $name = "Someone at " . $results3->row('user_company');
            if(strlen($name) > 40) $name = substr($name,0,37)."...";
            print $name;
        } else {
            echo "Anonymous";
        }
        echo "</span></span><span style='margin-left:5px;'> " . $ipaddr['ipaddr'] . ", ". substr($ipaddr['browser'],0,40) . "...</span>";
    }

    $results2 = $this->EE->db->query("
						SELECT
							a.action as action,
							a.ipaddr,
							a.user_id,
							a.entry_id,
							a.email_id,
							concat(d.m_field_id_4,' ',d.m_field_id_5,' at ',d.m_field_id_6) as user_text,
							c.title as report_title,
							a.item_text,
							b.subject,
							a.timestamp
						FROM
							exp_log_all AS a
							LEFT OUTER JOIN exp_member_data AS d ON a.user_id = d.member_id
							LEFT OUTER JOIN exp_channel_titles AS c ON a.entry_id = c.entry_id
							LEFT OUTER JOIN exp_email_text AS b ON a.email_id = b.id
						WHERE
							a.ipaddr = '".$ipaddr['ipaddr']."'
						ORDER BY
							a.timestamp DESC
						LIMIT 5
			");

    foreach($results2->result_array() as $row){



        if($row['action'] == 'report_download'){
            $reporttitle = $row['report_title'];
            if(strlen($reporttitle) > 30) $reporttitle = substr($reporttitle,0,27)."...";
            echo "
					<br>
					  <div class='type' style='background-color: #CBE0A9;'>Downloaded</div><div class='type_text'>".($row['entry_id']=='0' ? $row['item_text'] : "<a href='/admin_email/activity_report/".$row['entry_id']."/'>".$reporttitle." </a>" )."</div><div class='type_time'><i> - <abbr class='timeago' title='".date('Y-m-d\TH:i:s', strtotime($row['timestamp'])-$offset)."'></abbr></i></div>
				";
        } elseif($row['action'] == 'sample_download'){
            $reporttitle = $row['item_text'];
            if(strlen($reporttitle) > 30) $reporttitle = substr($reporttitle,0,27)."...";
            echo "
					<br>
					  <div class='type' style='background-color: #CBE0A9;'>Downloaded</div><div class='type_text'><a href='/admin_email/activity_report/".$row['entry_id']."/'>".$reporttitle." </a></div><div class='type_time'><i> - <abbr class='timeago' title='".date('Y-m-d\TH:i:s', strtotime($row['timestamp'])-$offset)."'></abbr></i></div>
				";
        } elseif($row['action'] == 'web_visited') {
            $visited = $row['item_text'];
            if(strlen($visited ) > 30) $visited = substr($visited ,0,27)."...";
            echo "
					<br>
					  <div class='type' style='background-color: #bef2ff;'>Viewed</div><div class='type_text'><a href='/".$row['item_text']."/'>";
            if($row['item_text'] == '') echo "Homepage"; else echo $visited;

            echo " </a></div><div class='type_time'><i> - <abbr class='timeago' title='".date('Y-m-d\TH:i:s', strtotime($row['timestamp'])-$offset)."'></abbr></i></div>";
        } elseif($row['action'] == 'email_open') {
            $opened = $row['subject'];
            if(strlen($opened ) > 30) $opened = substr($opened  ,0,27)."...";
            echo "
					<br>
					  <div  class='type' style='background-color: #f0f0f0;'>Opened e-mail</div><div class='type_text'><a href='/admin_email/emails/".$row['email_id']."/'>".$opened."</a></div><div class='type_time'><i> - <abbr class='timeago' title='".date('Y-m-d\TH:i:s', strtotime($row['timestamp'])-$offset)."'></abbr></i></div>
				";
        }
    }

    echo "</div>";

}

echo "</div>";

?>


<script type="text/javascript">
    jQuery(document).ready(function($){
        $('abbr.timeago').timeago();
    });
</script>


{if:else}
{admin_authrequired}
{/if}