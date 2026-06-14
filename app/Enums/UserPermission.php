<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermission: string
{
    // ── Forum ─────────────────────────────────────────
    case ForumQuestionCreate = 'forum.question.create';
    case ForumQuestionEdit   = 'forum.question.edit';
    case ForumQuestionDelete = 'forum.question.delete';
    case ForumQuestionPin    = 'forum.question.pin';
    case ForumAnswerCreate   = 'forum.answer.create';
    case ForumAnswerDelete   = 'forum.answer.delete';
    case ForumAnswerAccept   = 'forum.answer.accept';
    case ForumVote           = 'forum.vote';
    case ForumCommentCreate  = 'forum.comment.create';
    case ForumCommentDelete  = 'forum.comment.delete';
    case ForumReport         = 'forum.report';

    // ── Blog ──────────────────────────────────────────
    case BlogArticleCreate    = 'blog.article.create';
    case BlogArticleEdit      = 'blog.article.edit';
    case BlogArticleDelete    = 'blog.article.delete';
    case BlogArticleSubmit    = 'blog.article.submit';
    case BlogArticlePublish   = 'blog.article.publish';
    case BlogArticleUnpublish = 'blog.article.unpublish';
    case BlogCommentCreate    = 'blog.comment.create';
    case BlogCommentDelete    = 'blog.comment.delete';
    case BlogResourceUpload   = 'blog.resource.upload';
    case BlogResourceDownload = 'blog.resource.download';
    case BlogResourceDelete   = 'blog.resource.delete';

    // ── Events ────────────────────────────────────────
    case EventRegister = 'event.register';
    case EventCancel   = 'event.cancel';
    case EventCreate   = 'event.create';
    case EventManage   = 'event.manage';

    // ── Jobs (membre) ─────────────────────────────────
    case JobApply        = 'job.apply';
    case JobFavorite     = 'job.favorite';
    case JobOfferCreate  = 'job.offer.create';
    case JobOfferManage  = 'job.offer.manage';
    case JobOfferPublish = 'job.offer.publish';

    // ── Company (compte entreprise) ───────────────────
    case CompanyOfferCreate    = 'company.offer.create';
    case CompanyOfferManage    = 'company.offer.manage';
    case CompanyViewApplicants = 'company.view.applicants';
    case CompanyDownloadCv     = 'company.download.cv';

    // ── Modération ────────────────────────────────────
    case ModerationReportHandle = 'moderation.report.handle';
    case ModerationContentHide  = 'moderation.content.hide';
    case ModerationUserSuspend  = 'moderation.user.suspend';

    // ── Admin ─────────────────────────────────────────
    case AdminAccess     = 'admin.access';
    case AdminUserManage = 'admin.user.manage';
    case AdminUserBan    = 'admin.user.ban';
    case AdminSettings   = 'admin.settings';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** Retourne les permissions associées à un rôle. */
    public static function forRole(UserRole $role): array
    {
        return match ($role) {
            UserRole::SuperAdmin => self::values(),

            UserRole::Admin => array_values(array_filter(
                self::values(),
                fn (string $p) => $p !== self::AdminSettings->value,
            )),

            UserRole::Moderator => array_unique(array_merge(
                [
                    self::ForumQuestionPin->value,
                    self::ForumQuestionDelete->value,
                    self::ForumAnswerDelete->value,
                    self::ForumCommentDelete->value,
                    self::BlogArticlePublish->value,
                    self::BlogArticleUnpublish->value,
                    self::BlogCommentDelete->value,
                    self::BlogResourceDelete->value,
                    self::ModerationReportHandle->value,
                    self::ModerationContentHide->value,
                    self::ModerationUserSuspend->value,
                    self::AdminAccess->value,
                ],
                self::forRole(UserRole::Member) // hérite de toutes les permissions membre
            )),

            UserRole::Member => [
                self::ForumQuestionCreate->value,
                self::ForumQuestionEdit->value,
                self::ForumQuestionDelete->value,
                self::ForumAnswerCreate->value,
                self::ForumAnswerDelete->value,
                self::ForumAnswerAccept->value,
                self::ForumVote->value,
                self::ForumCommentCreate->value,
                self::ForumCommentDelete->value,
                self::ForumReport->value,
                self::BlogArticleCreate->value,
                self::BlogArticleEdit->value,
                self::BlogArticleDelete->value,
                self::BlogArticleSubmit->value,
                self::BlogCommentCreate->value,
                self::BlogCommentDelete->value,
                self::BlogResourceUpload->value,
                self::BlogResourceDownload->value,
                self::BlogResourceDelete->value,
                self::EventRegister->value,
                self::EventCancel->value,
                self::JobApply->value,
                self::JobFavorite->value,
                self::JobOfferCreate->value,
            ],

            UserRole::Company => [
                self::CompanyOfferCreate->value,
                self::CompanyOfferManage->value,
                self::CompanyViewApplicants->value,
                self::CompanyDownloadCv->value,
            ],
        };
    }
}
