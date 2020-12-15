<?php

        foreach($tours_country as $key => $value)
		{
            $options .=  '<option value="'.$value['id'].'">'.$value['name'].'</option>';
		}
		echo  $options;
?>