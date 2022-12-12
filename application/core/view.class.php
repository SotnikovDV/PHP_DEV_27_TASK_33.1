<?php
class View
{
	function generate($content_view, $template_view, $data = null)
	{
		include 'application/views/'.$template_view;
		//echo 'генерация view'.$template_view;
	}
	function simple($content_view)
	{
		include 'application/views/'.$content_view;
	}
}