<?php
class MLCApiEvernote extends MLCApiClassBase{
	protected $strClassName = 'MLCApiEvernote';
	
	public function  __call($strName, $arrArguments) {
        
		
        
     }
	public function FinalAction($arrPostData){

		$arrTasks = MLCHRTask::LoadByUrl('/tasks.xml');

		return new MLCApiResponse($arrTasks);
	}
    	
	public function Query(){
	 	//Will need to accept QS Pramaeters of facebook, twitter, google
	}
}
abstract class MLCApiFunnelQS{
	const StartDate = 'start';
	const EndDate = 'end';
}
