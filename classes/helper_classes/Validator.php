<?php
class Validator
{
       protected $di;
       protected $database;
       protected $errorHandler;
       protected $rules = ["required", "minlength", "maxlength", "unique", "email"];
       protected $messages = [
              "required" => "This :field field is required",
              "minlength" => "The :field field must be a minimum of :satisfier characters",
              "maxlength" => "The :field field must be a maximum of :satisfier characters",
              "email" => "This is not a valid email address",
              "unique" => "This :field is already taken"
       ];
       /**
        * Validator Constructor.
        * @param  Database $database
        * @param ErrorHandler $errorHandler
       */
       public function __construct(DependencyInjector $di)
       {
              $this->di = $di;
              $this->database = $this->di->get('database');
              $this->errorHandler = $this->di->get('errorHandler');
       } 
       public function check($items, $rules)
       {
              foreach($items as $item=>$value)
              {
                     if(in_array($item, array_keys($rules)))
                     {
                            $this->validate([
                                   'field' => $item,
                                   'value' => $value,
                                   'rules' => $rules[$item]
                            ]);
                     }
              }
              return $this;       
       }
       

       public function fails(){
              return $this->errorHandler->hasErrors();
       }
       public function errors(){
              return $this->errorHandler;
       }
       private function validate($item){
              /**
               * 
              */
              $field = $item['field'];
              $value = $item['value'];
              foreach($item['rules'] as $rule=>$satisfier)
              {
                     if(!call_user_func_array([$this,$rule], [$field, $value, $satisfier]))
                     {
                            // store the error in the error handler
                            $this->errorHandler->addError(str_replace([':field', ':satisfier'],
                            [$field,$satisfier],$this->messages[$rule]), $field);
                     }
              }
       }
       private function required($field, $value, $satisfier)
       {
              return !empty(trim($value));
       }
       private function minlength($field, $value, $satisfier)
       {
              return mb_strlen($value)>=$satisfier;
       }
       private function maxlength($field, $value, $satisfier)
       {
              return mb_strlen($value)<=$satisfier;
       }
       private function unique($field, $value, $satisfier)
       {
              return !$this->database->exists($satisfier,[$field=>$value]);
       }
       private function email($field, $value, $satisfier)
       {
              return filter_var($value, FILTER_VALIDATE_EMAIL);
       }

}
?>