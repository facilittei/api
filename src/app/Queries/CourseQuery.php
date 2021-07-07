<?php

namespace App\Queries;

class CourseQuery
{
    /**
     * Build a query to get the number of lessons taught by a specific teacher.
     *
     * @return string
     */
    public static function buildGetTotalLessons()
    {
        $query = <<<QUERY
        SELECT courses.id, COUNT(lessons.id) total
        FROM lessons
        INNER JOIN chapters ON chapters.id = lessons.chapter_id
        INNER JOIN courses ON courses.id = chapters.course_id
        WHERE (
            courses.user_id = ?
            OR
            courses.id IN (
                SELECT courses.id FROM courses
                INNER JOIN course_user ON courses.id = course_user.course_id
                WHERE course_user.user_id = ?
            )
        )
        GROUP BY courses.id;
        QUERY;

        return $query;
    }

    /**
     * Build a query to get the number of favorited lessons by courses.
     *
     * @return string
     */
    public static function buildGetTotalFavorites()
    {
        $query = 'SELECT courses.id, COUNT(lessons.id) AS total FROM courses ';
        $query .= 'INNER JOIN chapters ON courses.id = chapters.course_id ';
        $query .= 'INNER JOIN lessons ON chapters.id = lessons.chapter_id ';
        $query .= 'INNER JOIN favorite_lesson ON lessons.id = favorite_lesson.lesson_id ';
        $query .= 'WHERE courses.user_id = ? AND courses.deleted_at IS NULL ';
        $query .= 'AND lessons.deleted_at IS NULL AND chapters.deleted_at IS NULL ';
        $query .= 'GROUP BY lessons.id ';

        return $query;
    }

    /**
     * Build a query to get the number of comments by courses.
     *
     * @return string
     */
    public static function buildGetTotalComments()
    {
        $query = 'SELECT courses.id, COUNT(courses.id) AS total FROM courses ';
        $query .= 'INNER JOIN comments ON courses.id = comments.course_id ';
        $query .= 'WHERE courses.user_id = ? AND courses.deleted_at IS NULL  ';
        $query .= 'GROUP BY courses.id ';

        return $query;
    }
}
