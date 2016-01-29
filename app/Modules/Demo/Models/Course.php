<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Course extends BaseModel
{
    protected $table = 'courses';

    protected $relations = array('students');


    public function __construct()
    {
        parent::__construct();
    }

    public function students()
    {
        return $this->belongsToMany('App\Modules\Demo\Models\Student', 'course_student', 'course_id', 'student_id');
    }

}
