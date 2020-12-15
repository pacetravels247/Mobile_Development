<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/ckeditor/ckeditor.js"></script>
<?php
//get sub admin table
$table = '<div>
				<table class="table table-condensed">
				<tr>
					<th>Page Title</th>
					<th>Page SEO Title</th>
					<th>Page SEO Keyword</th>
					<th>Page SEO Description</th>
					<th>Page Position</th>
					<th>Status</th>
					<th>Action</th>
				</tr>';
	if (valid_array($sub_admin) == true) {
		foreach ($sub_admin as $key => $value) {
			$action = '<a role="button" href="'.base_url().'index.php/cms/add_cms_page/'.$value['page_id'].'"><button class="btn btn-sm">Edit</button></a>';
			$action .= '<a role="button" href="'.base_url().'index.php/cms/delete_cms_page/'.$value['page_id'].'"><button class="btn btn-sm">Delete</button></a>';
			if (intval($value['page_status']) == 1) {
				$status_label = '<span class="label label-success">Active</span>';
				$status_button = '<a role="button" href="'.base_url().'index.php/cms/cms_status/'.$value['page_id'].'/D"><button class="label label-danger">Deactivate</button></a>'; 
			} else {
				$status_label = '<span class="label label-danger">Inactive</span>';
				$status_button = '<a role="button" href="'.base_url().'index.php/cms/cms_status/'.$value['page_id'].'/A"><button class="label label-success">Activate</button></a>';
			} 
			$table .= '<tr>
				<td>'.$value['page_title'].'</td>
				<td>'.$value['page_seo_title'].'</td>
				<td>'.$value['page_seo_keyword'].'</td>
				<td>'.$value['page_seo_description'].'</td>
				<td>'.$value['page_position'].'</td>
				<td>'.$status_label.'</td>
				<td>'.$action.' '.$status_button.'</td>
			</tr>';
		}
	} else {
		$table .= '<tr><td colspan="8">No Cms Page Found In The System</td></tr>';
	}
$table .= '</table></div>';
//end of table section
?>
<div class="bodyContent">
	<div class="panel <?=PANEL_WRAPPER?>"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<i class="fa fa-edit"></i> Static Page Content
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->

<form method="post" autocomplete="off" action="<?php echo base_url();?>index.php/cms/add_cms_page/<?php echo $ID;?>" id="profile_form">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-condensed">
 <tr>
    <td>Page Title<span class="text-danger">*</span></td>
    <td><input type="text" name="page_title" value="<?php echo isset($page_title) ? $page_title : '';?>">
    <font color="red" ><?=@form_error('page_title')?> </font>
    </td>
  </tr>
  <tr>
  	<td>Page Description<span class="text-danger">*</span></td>
    <td><textarea class="ckeditor" id="editor" name="page_description" rows="10" cols="80"><?php echo isset($page_description) ? $page_description : '';?></textarea>
     <font color="red" ><?=@form_error('page_description')?> </font>
    </td>
  </tr>
  <tr>
  	<td>Page SEO Title <span class="text-danger">*</span></td>
    <td><input type="text" name="page_seo_title" value="<?php echo isset($page_seo_title) ? $page_seo_title : '';?>">
     <font color="red" ><?=@form_error('page_seo_title')?> </font>
    </td>
  </tr>
  <tr>
    <td>Page SEO Keyword <span class="text-danger">*</span></td>
    <td><input type="text" name="page_seo_keyword" value="<?php echo isset($page_seo_keyword) ? $page_seo_keyword : '';?>">
    <font color="red" ><?=@form_error('page_seo_keyword')?> </font>
    </td>
   </tr>
  <tr>
    <td>Page SEO Description <span class="text-danger">*</span></td>
    <td><input type="text" name="page_seo_description" value="<?php echo isset($page_seo_description) ? $page_seo_description : '';?>">
    <font color="red" ><?=@form_error('page_seo_description')?> </font>
    </td>
   </tr>
  <tr>
    <td>Page Position <span class="text-danger">*</span></td>
    <td>
    <select name="page_position">
    	<option value="">Select</option>
    	<option value="Top" <?php if(isset($page_position)){ if($page_position == 'Top'){ echo 'selected="selected"'; }}?>>Top</option>
        <option value="Bottom" <?php if(isset($page_position)){ if($page_position == 'Bottom'){ echo 'selected="selected"'; }}?>>Bottom</option>
        <option value="Both" <?php if(isset($page_position)){ if($page_position == 'Both'){ echo 'selected="selected"'; }}?>>Both</option>
    </select>
    <font color="red" ><?=@form_error('page_position')?> </font>
    </td>
    </tr>
  <tr>
    <td colspan="3" align="center"><input type="submit" class="btn btn-sm btn-success" value="Submit"/></td>
  
  </tr>
</table>
</form>
</div>
</div>
</div>
<div class="bodyContent">
	<div class="panel <?=PANEL_WRAPPER?>"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<i class="fa fa-edit"></i> CMS Page List
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
<div class="panel-body">
<?php 
echo $table;
?>
</div>
</div></div></div>