<h1><?php 
	echo (!empty($details[0]->full_name)) ? $details[0]->full_name.' ('.$details[0]->username.')': $details[0]->username;
?></h1>

<div class="box">
    <h2>Talks</h2>
<?php if (count($talks) == 0): ?>
	<p>No talks so far</p>
<?php else: ?>
    <?php
        foreach($talks as $k=>$v){
        	$this->load->view('talk/_talk-row', array('talk'=>$v));
        }
    ?>
<?php endif; ?>
</div>

<div class="box">
    <h2>Comments</h2>
<?php if (count($comments) == 0): ?>
	<p>No comments so far</p>
<?php else: ?>
    <?php foreach($comments as $k=>$v): ?>
    <div class="row">
    	<strong><a href="/talk/view/<?php echo $v->talk_id; ?>#comment-<?php echo $v->ID; ?>"><?php echo escape($v->talk_title); ?></a></strong>
    	<div class="clear"></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<?php
//sort the events
$ev=array('attended'=>array(),'attending'=>array());
foreach($is_attending as $k=>$v){
	if($v->event_end<time()){
		$ev['attended'][]=$v; 
	}else{ $ev['attending'][]=$v; }
}
//minimize my attending
$my=array();
foreach($my_attend as $k=>$v){ $my[]=$v->ID; }

//check the date and, if they have talks in their list, be sure that its in the list
foreach($talks as $k=>$v){
	$d=array(
		'event_name'	=> $v->event_name,
		'event_start'	=> $v->date_given,
		'ID'			=> $v->eid
	);
	$d=(object)$d;
	if($v->date_given<time()){
		$ev['attended'][]=$d;
	}else{ $ev['attending'][]=$d; }
}
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td>
		<div class="box">
			<h2>Events They'll Be At</h2>
		<?php if (count($ev['attending']) == 0): ?>
			<p>No events so far</p>
		<?php else: ?>
		    <?php 
			$eids=array();
			foreach($ev['attending'] as $k=>$v){ 
				if(in_array($v->ID,$eids)){ continue; }else{ $eids[]=$v->ID; }
			?>
		    <div class="row">
		    	<strong><a href="/event/view/<?php echo $v->ID; ?>"><?php echo escape($v->event_name); ?></a></strong>
				<?php echo date('M d, Y',$v->event_start); ?>
				<?php if(in_array($v->ID,$my)){ echo "<br/><span style=\"color:#92C53E;font-size:11px\">you'll be there!</span>"; } ?>
		    	<div class="clear"></div>
		    </div>
		    <?php } ?>
		<?php endif; ?>
		</div>
	</td>
	<td>
		<div class="box">
			<h2>Events They Were At</h2>
		<?php if (count($ev['attended']) == 0): ?>
			<p>No events so far</p>
		<?php else: ?>
		    <?php 
			$eids=array();
			foreach($ev['attended'] as $k=>$v){
				if(in_array($v->ID,$eids)){ continue; }else{ $eids[]=$v->ID; }
			?>
		    <div class="row">
		    	<strong><a href="/event/view/<?php echo $v->ID; ?>"><?php echo escape($v->event_name); ?></a></strong>
				<?php echo date('M d, Y',$v->event_start); ?>
				<?php if(in_array($v->ID,$my)){ echo "<br/><span style=\"color:#92C53E\">you were there!</span>"; } ?>
		    	<div class="clear"></div>
		    </div>
		    <?php } ?>
		<?php endif; ?>
		</div>
	</td>
</tr>
<?php if($is_admin){ ?>
<tr>
	<td colspan="2">
		<div class="box">
			<h2>Admin</h2>
			<table cellpadding="3" cellspacing="0" border="0">
			<?php
			//echo '<pre>'; print_r($uadmin); echo '</pre>';
			foreach($uadmin as $k=>$v){
				if(!isset($v->detail[0])){ continue; }
				if($v->rtype=='talk'){
					$title=$v->detail[0]->talk_title;
					$url='/talk/view/'.$v->detail[0]->ID;
				}else{ 
					$title=$v->detail[0]->event_name;
					$url='/event/view/'.$v->detail[0]->ID;
				}
				$pend=($v->rcode=='pending') ? ' (pending)':'';
				echo sprintf('
					<tr id="resource_row_%s">
						<td style="padding:3px">%s</td>
						<td style="padding:3px"><a href="%s">%s %s</a></td>
						<td style="padding:3px"><a href="#" onClick="removeRole(%s);return false;">X</a></td>
					</tr>
				',$v->rid,$v->rtype,$url,$title,$pend,$v->admin_id);
			}
			?>
			</table>
			
			<b>Add/Remove Permissions</b><br/>
			<table cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td style="padding:3px">Type:</td>
				<td style="padding:3px">
					<select name="add_type" id="add_type" onChange="populateEvents('event_names');">
						<option value="">Select Type
						<option value="talk">Talk
						<option value="event">Event
					</select>
				</td>
			</tr>
			<tr>
				<td style="padding:3px">Event:</td>
				<td style="padding:3px">	
					<select name="event_names" id="event_names" onChange="chkAdminType('event_talks')">
					<option value="">Select Event
					</select>
				</td>
			</tr>
			<tr id="talks_row" style="display:none">
				<td style="padding:3px">Talks:</td>
				<td style="padding:3px">
					<select name="event_talks" id="event_talks">
						<option value="">Select Talk
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right" style="padding:3px">
					<input type="button" name="add_role" value="add" onClick="addRole(<?php echo $details[0]->ID; ?>)"/>
				</td>
			</tr>
			</table>
		</div>
	</td>
</tr>
<?php } ?>
</table>