<?php
error_reporting(E_ALL);

require 'includes/sql.class.php';
$sql = new sql;

if($_GET['i'] == 1){

    $title = "scrubby";
    $type = "tutorial";
    $desc = "You be too scrubby to view";
    $time = time();
    
    $shiet['data0'] = 1;
    $shiet['data1'] = 1;
    $shiet['data2'] = 1;
    $shiet['lang0'] = '1';
    $shiet['lang1'] = '10';
    $shiet['lang2'] = '20';
    
    $stit = $sql->dbh->prepare("INSERT INTO `".$sql->tbl['code']."` (`title`, `user_id`, `date`, `public`, `desc`, `type`) VALUES ( :title, :userid, :date, :public, :desc, :type)");
    $stit->bindParam('title', $title);
    $stit->bindParam('userid', $_COOKIE["userid"]);
    $stit->bindParam('date', $time);
    $stit->bindParam('public', $_POST["public"]);
    $stit->bindParam('desc', $desc);
    $stit->bindParam('type', $type);
    echo "(".$res = $stit->execute().")";
    if($res){
        $idst = $sql->dbh->query("SELECT id FROM `".$sql->tbl['code']."` WHERE title = '".$title."' AND date = ".$time);
        $id = $idst->fetch()['id'];
        for($c = 0;isset($shiet['lang'.$c]);$c++){
            $stat = $sql->dbh->prepare("INSERT INTO `".$sql->tbl['languages']."` (languageid, projectid) VALUES (:langid, :projid)");
            $stat->bindParam("langid", $shiet['data'.$c]);
            $stat->bindParam("projid", $id);
            $stat->execute();
        }
    }else{
        
    }
    print_r($sql->dbh->errorInfo());
}else if($_GET['i'] == 2){
    $title = "feget";
    $userid = "5";
    $date = time();
    $public = 1;
    $desc = "You be";
    $type = "tutorial";
    $q = $sql->dbh->prepare("INSERT INTO `".$sql->tbl["code"]."` (`title`, `user_id`, `date`, `public`, `desc`, `type`) 
        VALUES (:title, :userid, :date, :public, :desc, :type)");
    $suc = $q->execute(array('title'=>$title, 'userid'=>$userid, 'date'=>$date, 'public'=>$public, 'desc'=>$desc, 'type'=>$type));
    echo "INSERT INTO `".$sql->tbl["code"]."` (title, `user_id`, date, public, desc, type) 
        VALUES (:title, :userid, :date, :public, :desc, :type)";
    if($suc == 1){
        echo "jah";
    }else{
        echo "nein";
        echo $suc;
        print_r($sql->dbh->errorInfo());
    }
}else if($_GET['i'] == 3){
    $q = $sql->dbh->prepare("SELECT `id` FROM `".$sql->tbl["users"]."` WHERE username = '".$_COOKIE['username']."'");
    $q->execute();
    $id = $q->fetch()['id'];
}else if($_GET['i'] == 4){
    $str =  'package com.jn1234.minecraftercity;

import java.util.Arrays;

import android.app.Activity;
import android.graphics.Paint;
import android.graphics.Typeface;
import android.os.Bundle;
import android.widget.LinearLayout;
import android.widget.TextView;

public class HomePage extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		setContentView(R.layout.mcc);
		TextView tv = (TextView) findViewById(R.id.tvTitle1);
		TextView tv2 = (TextView) findViewById(R.id.tvTitle2);
		TextView tv3 = (TextView) findViewById(R.id.tvTitle3);
		TextView tvOnlinePlayers = (TextView) findViewById(R.id.tvOnlinePlayers);
		TextView tvOnlinePlayers2 = (TextView) findViewById(R.id.tvOnlinePlayers2);
		TextView tvOnlinePlayers1 = (TextView) findViewById(R.id.tvOnlinePlayers1);
		//News shit:
		TextView tvNews1Header = (TextView) findViewById(R.id.tvNews1Header);
		TextView tvNews1Content = (TextView) findViewById(R.id.tvNews1Content);
		TextView tvNews1Author = (TextView) findViewById(R.id.tvNews1Author);
		TextView tvNews2Header = (TextView) findViewById(R.id.tvNews2Header);
		TextView tvNews2Content = (TextView) findViewById(R.id.tvNews2Content);
		TextView tvNews2Author = (TextView) findViewById(R.id.tvNews2Author);
		TextView tvNews3Header = (TextView) findViewById(R.id.tvNews3Header);
		TextView tvNews3Content = (TextView) findViewById(R.id.tvNews3Content);
		TextView tvNews3Author = (TextView) findViewById(R.id.tvNews3Author);
		
		Typeface font = Typeface.createFromAsset(getAssets(), "fonts/komikax.ttf");
		tv.setTypeface(font);
		tv2.setTypeface(font);
		tv3.setTypeface(font);
		tvOnlinePlayers.setTypeface(font);
		tvOnlinePlayers1.setTypeface(font);
		tvOnlinePlayers2.setTypeface(font);
		
		Bundle extras = getIntent().getExtras();
	//News input
		if(extras.getString("news") == "0"){
			tvNews1Content.setText("FAILED TO LOAD NEWS, ARE YOU CONNECTED TO INTERNET?");
			tvNews1Content.setTypeface(font);
		}else{
			String results = extras.getString("news");
			String[] arrayy = results.split("_a_");
			
			if(arrayy.length > 1){
                //Set a maxlength of the content that is displayed
                String content1 = "";
                String content2 = "";
                String content3 = "";
                int maxlength = 300;
                if(arrayy[2].length() > maxlength){
                    //Shorten the content
                    content1 = arrayy[2].substring(0, Math.min(arrayy[2].length(), maxlength));
                }else{
                    content1 = arrayy[2];
                }
                if(arrayy[5].length() > maxlength){
                    //Shorten the content
                    content2 = arrayy[5].substring(0, Math.min(arrayy[5].length(), maxlength));
                }else{
                    content2 = arrayy[5];
                }
                if(arrayy[8].length() > maxlength){
                    //Shorten the content
                    content3 = arrayy[8].substring(0, Math.min(arrayy[8].length(), maxlength));
                }else{
                    content3 = arrayy[8];
                }
                
				tvNews1Header.setText(arrayy[0]);
				tvNews1Author.setText("- " + arrayy[1]);
				tvNews1Content.setText(content1);
				tvNews2Header.setText(arrayy[3]);
				tvNews2Author.setText("- " + arrayy[4]);
				tvNews2Content.setText(content2);
				tvNews3Header.setText(arrayy[6]);
				tvNews3Author.setText("- " + arrayy[7]);
				tvNews3Content.setText(content3);
			}else{
				tvNews1Content.setText("FAILED TO LOAD NEWS, ARE YOU CONNECTED TO INTERNET?");
				tvNews1Content.setTypeface(font);
			}
		}
	//Online players input
		if(extras.getString("players") != null){
			if(extras.getString("players").equals("0")){
				tvOnlinePlayers.setText("JOIN US @ PLAY.MINECRAFTERCITY.COM\n\nYOU NEED TO BE CONNECTED TO INTERNET TO LOAD ONLINE PLAYERS");
			}else{
				String onlinePlayerList = extras.getString("players");
				String[] onlinePlayers = onlinePlayerList.split(",");
				String daList1 = "";
				String daList2 = "";
				//int[] partall = {0,2,4,6,8,10,12,14,16,18,20,22,24,26,28};
				int xx = 0;
				for(int zi = 0;zi < onlinePlayers.length;zi++){
					if(onlinePlayers[zi] == "0"){
						daList1 = daList1 + "Error\n";
					}else{
						if(xx == 0){
							daList1 = daList1 + onlinePlayers[zi] + "\n";
							xx = 1;
						}else{
							daList2 = daList2 + onlinePlayers[zi] + "\n";
							xx = 0;
						}
					}
				}
				tvOnlinePlayers1.setText(daList1);
				tvOnlinePlayers2.setText(daList2);
			}
		}else{
			tvOnlinePlayers.setText("JOIN US @ PLAY.MINECRAFTERCITY.COM\n\nAN ERROR OCCURED, FAILED TO LOAD ONLINE PLAYERS");
		}
		
	//Setting typefaces for the textviews
		tvNews1Header.setTypeface(font);
		tvNews1Author.setTypeface(font);
		tvNews2Header.setTypeface(font);
		tvNews2Author.setTypeface(font);
		tvNews3Header.setTypeface(font);
		tvNews3Author.setTypeface(font);
	}
    
    public void newsOnClickListener(TextView v){
        /*Grab the id, check if the id is from news 1, 2 or 3
        * Create an intents
        * i.putExtra("Title", "Title");
        * i.putExtra("Author", "Author");
        * i.putExtra("Content", "Content");
        * startActivity(i);
        */
        
    }
	
}
';
    $str = preg_replace('/([\r\n])/',"<br>", $str);
    $str = preg_replace('/([\r\t])/',"&nbsp;&nbsp;&nbsp;&nbsp;", $str);
    echo "$str";
}



?>