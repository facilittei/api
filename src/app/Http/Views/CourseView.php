<?php

namespace App\Http\Views;

class CourseView
{
    /**
     * Show user home dashboard report.
     *
     * @param  array  $report
     * @return array
     */
    public static function home(array $report)
    {
        $data = [];

        if (isset($report['teaching'])) {
            $teach = $report['teaching'];
            $courses = $teach['courses'];
            $drafts = $teach['drafts'];
            $sales = collect($teach['sales']);

            $teaching = [];
            $teaching['courses'] = [];
            $teaching['drafts'] = [];

            if (count($courses)) {
                $teaching['stats'] = [
                    'courses' => $teach['courses_total'],
                    'students' => $teach['students'],
                ];
            } else {
                $teaching['stats'] = [
                    'courses' => 0,
                    'students' => 0,
                ];
            }

            for ($i = 0; $i < count($courses); $i++) {
                $course = $courses[$i];
                $salesTotal = fn ($s) => count($s) > 0 ? floatval($s[0]->total) : 0;

                $teaching['courses'][] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'is_published' => $course->is_published,
                    'cover' => $course->cover,
                    'students' => CourseView::getCollectionByCourse($teach['courses_students'], $course->id),
                    'lessons' => CourseView::getCollectionByCourse($teach['courses_lessons'], $course->id),
                    'favorites' => CourseView::getCollectionByCourse($teach['favorites'], $course->id),
                    'comments' => CourseView::getCollectionByCourse($teach['comments'], $course->id),
                    'sales' => $salesTotal($sales->where('id', $course->id)),
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                ];
            }

            for ($i = 0; $i < count($drafts); $i++) {
                $draft = $drafts[$i];

                $teaching['drafts'][] = [
                    'id' => $draft->id,
                    'title' => $draft->title,
                    'is_published' => $draft->is_published,
                    'cover' => $draft->cover,
                    'students' => CourseView::getCollectionByCourse($teach['courses_students'], $draft->id),
                    'lessons' => CourseView::getCollectionByCourse($teach['courses_lessons'], $draft->id),
                    'created_at' => $draft->created_at,
                    'updated_at' => $draft->updated_at,
                ];
            }

            $data['teaching'] = $teaching;
        }

        if (isset($report['learning'])) {
            $learn = $report['learning'];
            $courses = $learn['courses'];

            $learning = [];
            $learning['latest_watched'] = CourseView::formatLatestWatchedLesson($learn['latest_watched']);
            $learning['stats'] = [
                'courses' => $learn['courses_total'],
            ];

            for ($i = 0; $i < count($courses); $i++) {
                $course = $courses[$i];

                $learning['courses'][] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'cover' => $course->cover,
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                    'students' => CourseView::getCollectionByCourse($learn['courses_students'], $course->id),
                    'lessons' => CourseView::getCollectionByCourse($learn['courses_lessons'], $course->id),
                    'favorites' => CourseView::getCollectionByCourse($learn['favorites'], $course->id),
                    'comments' => CourseView::getCollectionByCourse($learn['comments'], $course->id),
                ];
            }

            $data['learning'] = $learning;
        }

        return $data;
    }

    /**
     * Get the course related total count.
     *
     * @param  array  $collection
     * @param  int  $courseId
     * @return int
     */
    public static function getCollectionByCourse($collection, $courseId)
    {
        $result = 0;

        for ($i = 0; $i < count($collection); $i++) {
            if (! isset($collection[$i]->id)) {
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
     * @param  array  $collection
     * @param  int  $courseId
     * @return array
     */
    public static function formatLatestWatchedLesson($collection)
    {
        if (count($collection) > 0) {
            return [
                'course' => [
                    'id' => $collection[0]->course_id,
                    'title' => $collection[0]->course_title,
                ],
                'chapters' => [
                    'id' => $collection[0]->chapter_id,
                    'title' => $collection[0]->chapter_title,
                ],
                'lessons' => [
                    'id' => $collection[0]->lesson_id,
                    'title' => $collection[0]->lesson_title,
                ],
            ];
        }

        return null;
    }

    /**
     * Format course stats.
     *
     * @param  array  $watcheds
     * @param  array  $lessons
     * @return array
     */
    public static function formatCourseStats($watcheds, $lessons)
    {
        $watchedStats = [];
        for ($i = 0; $i < count($watcheds); $i++) {
            $total = $watchedStats[$watcheds[$i]->course_id] ?? 0;
            $watchedStats[$watcheds[$i]->course_id] = ++$total;
        }

        $lessonStats = [];
        for ($i = 0; $i < count($lessons); $i++) {
            $lessonStats[$lessons[$i]->course_id] = $lessons[$i]->total;
        }

        $stats = [];
        foreach ($lessonStats as $key => $value) {
            if (isset($watchedStats[$key])) {
                $stats[$key] = floor($watchedStats[$key] / $value * 100);
            }
        }

        return $stats;
    }
}
