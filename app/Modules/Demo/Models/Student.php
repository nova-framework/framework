<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Student extends BaseModel
{
    protected $tableName = 'students';

    protected $relations = array('courses');


    public function __construct()
    {
        parent::__construct();
    }

    public function courses()
    {
        return $this->belongsToMany('App\Modules\Demo\Models\Course', 'course_student', 'student_id', 'course_id');
    }

}
