<?php

namespace App\Http\Presenters;

class CoursePresenter
{
    /**
     * Show user home dashboard report.
     *
     * @param array $report
     * @return array
     */
    public static function home(array $report)
    {
        $data = [];

        if (isset($report['teaching'])) {
            $teach = $report['teaching'];
            $courses = $teach['courses'];

            $teaching = [];
            $teaching['courses'] = [];

            if (count($courses)) {
                $teaching['students'] = $teach['students'];
            }

            for ($i = 0; $i < count($courses); $i++) {
                $course = $courses[$i];

                $teaching['courses'][] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'students' => CoursePresenter::getCollectionByCourse($teach['courses_students'], $course->id),
                    'lessons' => CoursePresenter::getCollectionByCourse($teach['courses_lessons'], $course->id),
                    'favorites' => CoursePresenter::getCollectionByCourse($teach['favorites'], $course->id),
                    'comments' => CoursePresenter::getCollectionByCourse($teach['comments'], $course->id),
                ];
            }

            $data['teaching'] = $teaching;
        }

        if (isset($report['learning'])) {
            $data['learning'] = $report['learning'];
            $data['learning']['latestWatched'] = CoursePresenter::formatLatestWatchedLesson($report['learning']['latestWatched']);
        }

        return $data;
    }

    /**
     * Get the course related total count.
     *
     * @param array $collection
     * @param int $courseId
     * @return int
     */
    public static function getCollectionByCourse($collection, $courseId)
    {
        $result = 0;

        for ($i = 0; $i < count($collection); $i++) {
            if (!isset($collection[$i]->id)) {
                continue;
            }

            if ($collection[$i]->id === $courseId) {
                return $collection[$i]->total ?? $result;
            }
        }

        return $result;
    }

    /**
     * Format latest watched lesson.
     *
     * @param array $collection
     * @param int $courseId
     * @return int
     */
    public static function formatLatestWatchedLesson($collection)
    {
        if (count($collection) > 0) {
            return [
                'course' => [
                    'id' => $collection[0]->course_id,
                    'title' => $collection[0]->course_title,
                    'chapters' => [
                        'id' => $collection[0]->chapter_id,
                        'title' => $collection[0]->chapter_title,
                        'lessons' => [
                            'id' => $collection[0]->lesson_id,
                            'title' => $collection[0]->lesson_title,
                        ]
                    ],
                ]
            ];
        }

        return [];
    }
}
