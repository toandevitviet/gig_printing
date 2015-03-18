<?

class ControllerGiftHellogift extends Controller{ 
    public function index(){
        // VARS
        $template="gift/hello.tpl"; // .tpl location and file
        $this->load->model('gift/hello');
        $this->template = ''.$template.'';
        $this->children = array(
            'common/header',
            'common/footer'
        );      
        $this->response->setOutput($this->render());
    }
}
?>