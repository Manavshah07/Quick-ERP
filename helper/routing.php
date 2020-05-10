<?php
require_once 'init.php';
if(isset($_POST['add_category']))
{
    if(Util::verifyCSRFToken($_POST))
    {
        
        $result = $di->get('category')->addCategory($_POST);
        
        switch($result)
        {
            case ADD_ERROR:
                Session::setSession(ADD_ERROR,"Add Category Error");
                Util::redirect("manage-category.php");
                break;
            case ADD_SUCCESS:
                Session::setSession(ADD_SUCCESS,"Add Category Success!");
                Util::redirect("manage-category.php");
                break;
            case VALIDATION_ERROR:
                Session::setSession('validation',"Validation Error");
                Session::setSession('old',$_POST);// Aray hy toh directly bhej sakte
                Session::setSession('errors',serialize($di->get('category')->getValidator()->errors()));//obj hy isiliye serialize kar k bheja
                Util::redirect("add-category.php");
                break;
        }
    }
    else{
        Session::setSession("csrf","CSRF Error");
        Util::redirect("manage-category.php");//Need to change this, actually we will be redirecting to some error page indicating Unauthorized access
        
    }
}

if(isset($_POST['page']))
{
    if($_POST['page'] == 'manage_category')
    {
        $dependency = 'category';
    }
    elseif($_POST['page'] == 'manage_customer'){
        
        $dependency = 'customer';
    }
    //&_POST['search']['value']
    //&_POST['start']
    //&_POST['length']
    //&_POST['order']
    //&_POST['draw']
    // Util::dd($dependency);
    $search_parameter = $_POST['search']['value'] ?? null;
    $order_by = $_POST['order'] ?? null;
    $start = $_POST['start'];
    $length = $_POST['length'];
    $draw = $_POST['draw'];
    
    $di->get($dependency)->getJSONDataForDataTable($draw,$search_parameter,$order_by,$start,$length);
    
}
if(isset($_POST['fetch']))
{
    //Util::dd($_POST['fetch']);
    if($_POST['fetch'] == 'category')
    {
        $category_id = $_POST['category_id'];
        $result = $di->get('category')->getCategoryByID($category_id,PDO::FETCH_ASSOC);
        //Util::dd($result[0]);
        echo json_encode($result[0]);
    }
}
if(isset($_POST['editCategory']))
{
    if(Util::verifyCSRFToken($_POST))
    {
        
        $result = $di->get('category')->update($_POST,$_POST['category_id']);

        //Util::dd($_POST['category_id']);
        
        switch($result)
        {
            case UPDATE_ERROR:
                Session::setSession(UPDATE_ERROR,"Update Category Error");
                Util::redirect("manage-category.php");
                break;
            case UPDATE_SUCCESS:
                Session::setSession(UPDATE_SUCCESS,"Update Category Success");
                Util::redirect("manage-category.php");
                break;
            case VALIDATION_ERROR:
                Session::setSession('validation',"Validation Error");
                Session::setSession('old',$_POST);
                Session::setSession('errors',serialize($di->get('category')->getValidator()->errors()));//object mai hai ya array hai to text mai store kar sakeee!
                Util::redirect("manage-category.php");
                break;
        }
    }else{
        //errorpage 
        Session::setSession("csrf","CSRF ERROR");
        Util::redirect("manage-category.php");//Need to change this, actually we be redirecting to some error page indicating Unauthorized access.

    }
}

?>
