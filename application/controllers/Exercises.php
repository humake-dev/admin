<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Exercises extends SL_photo
{
    protected $category_model = 'ExerciseCategory';
    protected $model = 'Exercise';
    protected $file_model='ExercisePicture';

    protected function set_add_form_data()
    {
        $this->index_data($this->input->get_post('category_id'));
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data($content['exercise_category_id']);

        $this->return_data['data']['category']['content'] = $content;
        $this->return_data['data']['category']['current_id'] = $content['exercise_category_id'];
        $this->return_data['data']['content'] = $content;
    }

    protected function set_insert_data($data)
    {
        $data['exercise_category_id'] = $data['exercise_category'];
        unset($data['exercise_category']);

        return $data;
    }

    protected function set_update_data($id, $data)
    {
        $data['id'] = $id;

        $data['exercise_category_id'] = $data['exercise_category'];
        unset($data['exercise_category']);

        return $data;
    }

    protected function add_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function edit_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('exercise_category', _('Exercise Category'), 'required|integer');
        $this->form_validation->set_rules('title', _('Title'), 'required|max_length[60]');
    }
}
