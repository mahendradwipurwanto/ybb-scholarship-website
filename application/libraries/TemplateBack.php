<?php
class TemplateBack
{
    protected $_ci;

    function __construct()
    {
        $this->_ci = &get_instance();
        $this->_ci->load->database();
    }

    function getSettingsValue($key)
    {
        $query = $this->_ci->db->get_where('tb_settings', ['key' => $key]);
        return $query->row()->value;
    }


    function countScholar(){
        $query = $this->_ci->db->get_where('tb_scholarship', ['status !=' => 2]);
        return $query->num_rows();
    }

    function view($content, $data = null)
    {
        $data['web_icon'] = $this->getSettingsValue('web_icon');
        $data['web_icon_white'] = $this->getSettingsValue('web_icon_white');
        $data['web_logo'] = $this->getSettingsValue('web_logo');
        $data['web_logo_white'] = $this->getSettingsValue('web_logo_white');

        $data['web_title'] = $this->getSettingsValue('web_title');
        $data['web_desc'] = $this->getSettingsValue('web_desc');
        $data['web_address'] = $this->getSettingsValue('web_address');

        $data['web_whatsapp'] = $this->getSettingsValue('web_whatsapp');
        $data['web_facebook'] = $this->getSettingsValue('web_facebook');
        $data['web_instagram'] = $this->getSettingsValue('web_instagram');
        $data['web_twitter'] = $this->getSettingsValue('web_twitter');
        $data['web_youtube'] = $this->getSettingsValue('web_youtube');

        $data['countScholar'] = $this->countScholar();

        $this->_ci->load->view('template/backend/header', $data);
        $this->_ci->load->view('template/alert', $data);
        $this->_ci->load->view('template/backend/navbar', $data);
        $this->_ci->load->view('template/backend/sidebar', $data);
        $this->_ci->load->view($content, $data);
        $this->_ci->load->view('template/backend/footer', $data);
    }
}
