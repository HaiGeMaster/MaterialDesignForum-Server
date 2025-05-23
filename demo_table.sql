-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2025-05-23 10:11:35
-- 服务器版本： 8.0.12
-- PHP 版本： 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `demo_table`
--

-- --------------------------------------------------------

--
-- 表的结构 `answer`
--

CREATE TABLE `answer` (
  `answer_id` int(10) UNSIGNED NOT NULL COMMENT '回答ID',
  `question_id` int(10) UNSIGNED NOT NULL COMMENT '问题ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `content_markdown` text NOT NULL COMMENT '原始的正文内容',
  `content_rendered` text NOT NULL COMMENT '过滤渲染后的正文内容',
  `comment_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回答表';

-- --------------------------------------------------------

--
-- 表的结构 `article`
--

CREATE TABLE `article` (
  `article_id` int(10) UNSIGNED NOT NULL COMMENT '文章ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `content_markdown` text COMMENT '原始的正文内容',
  `content_rendered` text COMMENT '过滤渲染后的正文内容',
  `comment_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `follower_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章表';

-- --------------------------------------------------------

--
-- 表的结构 `cache`
--

CREATE TABLE `cache` (
  `name` varchar(180) NOT NULL,
  `value` text NOT NULL,
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `life_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '有效时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='缓存表';

-- --------------------------------------------------------

--
-- 表的结构 `chat_group`
--

CREATE TABLE `chat_group` (
  `chat_group_id` int(10) UNSIGNED NOT NULL COMMENT '聊天组ID',
  `chat_group_name` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '聊天组名称',
  `chat_group_avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '聊天组头像',
  `chat_group_user_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '聊天组人数',
  `chat_group_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '聊天组简介',
  `chat_group_owner_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '聊天组创建者用户ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='聊天组表';

-- --------------------------------------------------------

--
-- 表的结构 `chat_groupable`
--

CREATE TABLE `chat_groupable` (
  `chat_groupable_id` int(10) UNSIGNED NOT NULL COMMENT '索引ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `chat_group_id` int(10) UNSIGNED NOT NULL COMMENT '加入的聊天组ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `delete_time` int(11) NOT NULL COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='聊天组加入关系表';

-- --------------------------------------------------------

--
-- 表的结构 `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(10) UNSIGNED NOT NULL COMMENT '评论ID',
  `commentable_id` int(10) UNSIGNED NOT NULL COMMENT '评论目标的ID',
  `commentable_type` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论目标类型：article、question、answer、文章、提问、回答',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '原始正文内容',
  `reply_count` int(11) NOT NULL DEFAULT '0' COMMENT '回复数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复与评论表';

-- --------------------------------------------------------

--
-- 表的结构 `domain_data`
--

CREATE TABLE `domain_data` (
  `index_id` int(10) UNSIGNED NOT NULL COMMENT '排序',
  `domain_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '域名',
  `first_activity_time` int(11) NOT NULL COMMENT '首次活动时间',
  `recent_activity_time` int(11) NOT NULL COMMENT '最近活动时间',
  `number_activities` int(11) NOT NULL COMMENT '活动次数',
  `allow_use` tinyint(1) NOT NULL COMMENT '允许使用',
  `renewal_expiration_date` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '续费到期时间',
  `recent_use_keys` varchar(29) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '最近使用的产品序列号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='域名使用数据表';

-- --------------------------------------------------------

--
-- 表的结构 `domain_key`
--

CREATE TABLE `domain_key` (
  `index_id` int(11) UNSIGNED NOT NULL COMMENT '排序',
  `renewal_key` varchar(29) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '产品续费序列号',
  `renewal_days` int(11) NOT NULL COMMENT '能续费的天数',
  `renewal_domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '续费域名',
  `renewal_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '续费者邮箱',
  `use_time` int(11) NOT NULL DEFAULT '0' COMMENT '使用时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='域名续费续时序列号';

-- --------------------------------------------------------

--
-- 表的结构 `follow`
--

CREATE TABLE `follow` (
  `follow_id` int(10) UNSIGNED NOT NULL COMMENT '关注ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `followable_type` char(10) NOT NULL COMMENT '关注目标类型 user、question、article、topic 用户、提问、文章、话题',
  `followable_id` int(10) UNSIGNED NOT NULL COMMENT '关注目标的ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章关注关系表';

-- --------------------------------------------------------

--
-- 表的结构 `image`
--

CREATE TABLE `image` (
  `key` varchar(50) NOT NULL COMMENT '图片键名',
  `filename` varchar(255) NOT NULL COMMENT '原始文件名',
  `width` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '原始图片宽度',
  `height` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '原始图片高度',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上传时间',
  `item_type` char(10) DEFAULT NULL COMMENT '关联类型：question、answer、article',
  `item_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `inbox`
--

CREATE TABLE `inbox` (
  `inbox_id` int(10) UNSIGNED NOT NULL COMMENT '私信ID',
  `sender_id` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发送者ID：system、user_id',
  `sender_type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发送者类型 user_to_user、user_to_chat_group、system_to_user、system_to_user_group',
  `receiver_id` int(10) UNSIGNED NOT NULL COMMENT '接收者ID：user_id、chat_group_id',
  `content_markdown` text NOT NULL COMMENT '原始的私信内容',
  `content_rendered` text NOT NULL COMMENT '过滤渲染后的私信内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送时间',
  `read_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读时间',
  `delete_time` int(11) NOT NULL COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='私信表';

-- --------------------------------------------------------

--
-- 表的结构 `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(10) UNSIGNED NOT NULL COMMENT '通知ID',
  `receiver_id` int(10) UNSIGNED NOT NULL COMMENT '接收者ID',
  `sender_id` int(11) NOT NULL COMMENT '发送者ID',
  `type` varchar(40) NOT NULL COMMENT '消息类型：\r\nquestion_answered, \r\nquestion_commented, \r\nquestion_deleted, \r\narticle_commented, \r\narticle_deleted, \r\nanswer_commented, \r\nanswer_deleted, \r\ncomment_replied, \r\ncomment_deleted',
  `content_markdown` text NOT NULL COMMENT '内容原文',
  `content_rendered` text NOT NULL COMMENT '内容正文',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  `question_id` int(11) NOT NULL COMMENT '提问ID',
  `answer_id` int(11) NOT NULL COMMENT '回答ID',
  `comment_id` int(11) NOT NULL COMMENT '评论ID',
  `reply_id` int(11) NOT NULL COMMENT '回复ID',
  `reply_to_reply_id` int(11) NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送时间',
  `read_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读时间',
  `delete_time` int(11) NOT NULL COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通知表';

-- --------------------------------------------------------

--
-- 表的结构 `option`
--

CREATE TABLE `option` (
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '字段名',
  `value` text NOT NULL COMMENT '字段值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设置表';

--
-- 转存表中的数据 `option`
--

INSERT INTO `option` (`name`, `value`) VALUES
('default_language', 'zh_CN'),
('site_description', '基于 Vuetify2与MDUI2 的 Material Design 样式的社区;A community based on Vuetify And MDUI2 for Material Design styles'),
('site_gongan_beian', ''),
('site_icp_beian', ''),
('site_keywords', 'Material Design Forum,Vuetify,MDUI,MDForum'),
('site_name', 'Material Design Forum'),
('site_static_url', ''),
('smtp_host', 'smtp.qq.com'),
('smtp_password', ''),
('smtp_port', '465'),
('smtp_reply_to', ''),
('smtp_secure', 'ssl'),
('smtp_send_name', 'MDF'),
('smtp_username', ''),
('theme', 'MaterialDesignForum-Vuetify2'),
('theme_color_param', '{\"light\":{\"primary\":\"#2196F3\",\"secondary\":\"#FF9800\",\"accent\":\"#E91E63\"},\"dark\":{\"primary\":\"#2196F3\",\"secondary\":\"#FF9800\",\"accent\":\"#E91E63\"},\"name\":\"\"}'),
('theme_typed_param', '{\"header\":\"Message.Components.TextPlay.With\",\"body\":\"Message.Components.TextPlay.MaterialDesign,Message.Components.TextPlay.VueAsTheCore,Message.Components.TextPlay.ImplementedByVuetify,Message.Components.TextPlay.MoreElegant,Message.Components.TextPlay.UnlimitedDistance,Message.Components.TextPlay.CrossPlatform,Message.Components.TextPlay.DynamicResponsive\",\"footer_header\":\"Message.Components.TextPlay.TheWay\",\"footer_tail\":\"Message.Components.TextPlay.EnjoyCommunication\"}');

-- --------------------------------------------------------

--
-- 表的结构 `question`
--

CREATE TABLE `question` (
  `question_id` int(10) UNSIGNED NOT NULL COMMENT '问题ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `content_markdown` text COMMENT '原始的正文内容',
  `content_rendered` text COMMENT '过滤渲染后的正文内容',
  `comment_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `answer_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '回答数量',
  `follower_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `last_answer_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后回答时间',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问题表';

-- --------------------------------------------------------

--
-- 表的结构 `reply`
--

CREATE TABLE `reply` (
  `reply_id` int(10) UNSIGNED NOT NULL COMMENT '回复ID',
  `replyable_id` int(10) UNSIGNED NOT NULL COMMENT '回复目标的ID:comment_id、reply_id',
  `replyable_type` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '回复目标类型：comment、reply、评论、回复',
  `replyable_comment_id` int(11) NOT NULL COMMENT '回复目标的父项：评论ID',
  `replyable_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '回复目标用户id',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '原始正文内容',
  `reply_count` int(11) NOT NULL DEFAULT '0' COMMENT '回复数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复与评论表';

-- --------------------------------------------------------

--
-- 表的结构 `report`
--

CREATE TABLE `report` (
  `report_id` int(10) UNSIGNED NOT NULL,
  `reportable_id` int(10) UNSIGNED NOT NULL COMMENT '举报目标ID',
  `reportable_type` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '举报目标类型：question、article、answer、comment、user、reply、topic',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `reason` varchar(200) NOT NULL COMMENT '举报原因',
  `report_handle_state` int(10) NOT NULL DEFAULT '0' COMMENT '处理状态:未处理0、已处理删除1、已处理对象无违规2',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '举报时间',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报';

-- --------------------------------------------------------

--
-- 表的结构 `token`
--

CREATE TABLE `token` (
  `token` varchar(50) NOT NULL DEFAULT '' COMMENT 'token 字符串',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `device` varchar(600) NOT NULL DEFAULT '' COMMENT '登陆设备，浏览器 UA 等信息',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `expire_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户TOKEN';

-- --------------------------------------------------------

--
-- 表的结构 `topic`
--

CREATE TABLE `topic` (
  `topic_id` int(10) UNSIGNED NOT NULL COMMENT '话题ID',
  `user_id` int(11) NOT NULL COMMENT '话题创建者用户id',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '话题名称',
  `cover` varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '封面图片token',
  `description` varchar(1000) NOT NULL DEFAULT '' COMMENT '话题描述',
  `article_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章数量',
  `question_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '问题数量',
  `follower_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='话题表';

-- --------------------------------------------------------

--
-- 表的结构 `topicable`
--

CREATE TABLE `topicable` (
  `topic_id` int(10) UNSIGNED NOT NULL COMMENT '话题ID',
  `topicable_id` int(10) UNSIGNED NOT NULL COMMENT '话题关系对应的ID',
  `topicable_type` char(10) NOT NULL COMMENT '话题关系对应的类型 question、article',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `user_group_id` int(10) UNSIGNED NOT NULL DEFAULT '2' COMMENT '用户组ID',
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `email` varchar(320) NOT NULL COMMENT '邮箱',
  `avatar` varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '头像token',
  `cover` varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '封面图片token',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `create_ip` varchar(80) DEFAULT NULL COMMENT '注册IP',
  `create_location` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '注册地址',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(80) DEFAULT NULL COMMENT '最后登陆IP',
  `last_login_location` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '最后登录地址',
  `follower_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注我的人数',
  `followee_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的人数',
  `following_topic_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的话题数量',
  `following_article_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的文章数量',
  `following_question_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的问题数量',
  `topic_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的话题数量',
  `article_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的文章数量',
  `question_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的问题数量',
  `answer_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的回答数量',
  `comment_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的评论数量',
  `reply_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的回复数量',
  `notification_unread` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未读通知数量',
  `inbox_system` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '（暂停用）未读系统信息数量',
  `inbox_user_group` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '（暂停用）未读用户组信息数量',
  `inbox_private_message` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '（暂停用）未读私信数',
  `headline` varchar(40) DEFAULT NULL COMMENT '一句话介绍',
  `bio` varchar(160) DEFAULT NULL COMMENT '个人简介',
  `blog` varchar(255) DEFAULT NULL COMMENT '个人主页',
  `company` varchar(255) DEFAULT NULL COMMENT '公司名称',
  `location` varchar(255) DEFAULT NULL COMMENT '地址',
  `language` varchar(30) NOT NULL COMMENT '使用的语言',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '注册时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `disable_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '禁用时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `user_group_id`, `username`, `email`, `avatar`, `cover`, `password`, `create_ip`, `create_location`, `last_login_time`, `last_login_ip`, `last_login_location`, `follower_count`, `followee_count`, `following_topic_count`, `following_article_count`, `following_question_count`, `topic_count`, `article_count`, `question_count`, `answer_count`, `comment_count`, `reply_count`, `notification_unread`, `inbox_system`, `inbox_user_group`, `inbox_private_message`, `headline`, `bio`, `blog`, `company`, `location`, `language`, `create_time`, `update_time`, `disable_time`) VALUES
(1, 1, 'Admin', '2652549974@qq.com', '{\"original\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/1\\/original\\/88fe3a21d11588f63d6ba8ea20efcd71.png\",\"small\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/1\\/small\\/88fe3a21d11588f63d6ba8ea20efcd71.png\",\"middle\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/1\\/middle\\/88fe3a21d11588f63d6ba8ea20efcd71.png\",\"large\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/1\\/large\\/88fe3a21d11588f63d6ba8ea20efcd71.png\"}', '{\"original\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/original\\/default.png\",\"small\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/small\\/default.png\",\"middle\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/middle\\/default.png\",\"large\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/large\\/default.png\"}', '81dc9bdb52d04dc20036dbd8313ed055', '127.0.0.1', '本机地址    ', 1747965584, '127.0.0.1', '本机地址    ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'TestAdmin', 'bio', 'blog', 'company', 'location', 'zh_CN', 1688355914, 1688355914, 0),
(2, 2, 'User', '2967621911@qq.com', '{\"original\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/2\\/original\\/f54400331a048ac22268805e482e5693.png\",\"small\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/2\\/small\\/f54400331a048ac22268805e482e5693.png\",\"middle\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/2\\/middle\\/f54400331a048ac22268805e482e5693.png\",\"large\":\"\\/public\\/static\\/upload\\/user\\/avatars\\/2\\/large\\/f54400331a048ac22268805e482e5693.png\"}', '{\"original\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/original\\/default.png\",\"small\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/small\\/default.png\",\"middle\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/middle\\/default.png\",\"large\":\"\\/public\\/static\\/default\\/user\\/covers\\/1\\/large\\/default.png\"}', '81dc9bdb52d04dc20036dbd8313ed055', '127.0.0.1', '本机地址    ', 1746599675, '127.0.0.1', '本机地址    ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'TestUser', 'bio', 'blog', 'company', 'location', 'zh_CN', 1688355914, 1688355914, 0);

-- --------------------------------------------------------

--
-- 表的结构 `user_group`
--

CREATE TABLE `user_group` (
  `user_group_id` int(10) UNSIGNED NOT NULL COMMENT '用户组ID',
  `user_group_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'User' COMMENT '用户组名称',
  `user_group_description` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'User' COMMENT '用户组描述',
  `user_group_icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'mdi-account' COMMENT '用户组图标',
  `user_group_icon_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '显示用户组标识',
  `user_group_color` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '用户组颜色',
  `user_group_user_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户组人数',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL COMMENT '删除时间',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是管理员',
  `ability_normal_login` tinyint(1) NOT NULL DEFAULT '0' COMMENT '前台正常登录',
  `ability_admin_login` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台登录',
  `ability_admin_manage_user_group` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理用户组（真则显示）',
  `ability_admin_manage_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理用户（真则显示）',
  `ability_admin_manage_topic` tinyint(1) DEFAULT '0' COMMENT '是否可后台管理话题（真则显示）',
  `ability_admin_manage_question` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理问题（真则显示）',
  `ability_admin_manage_article` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理文章（真则显示）',
  `ability_admin_manage_comment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理评论（真则显示）',
  `ability_admin_manage_answer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理回答（真则显示）',
  `ability_admin_manage_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理回复（真则显示）',
  `ability_admin_manage_report` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理举报（真则显示）',
  `ability_admin_manage_option` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可后台管理设置（真则显示）',
  `ability_create_article` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可发表文章',
  `ability_create_question` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可发表问题',
  `ability_create_answer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可发表回答',
  `ability_create_comment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可发表评论',
  `ability_create_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可发表回复',
  `ability_create_topic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可创建话题',
  `ability_edit_own_article` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可编辑自己的文章',
  `ability_edit_own_question` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可编辑自己的问题',
  `ability_edit_own_answer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可编辑自己的回答',
  `ability_edit_own_comment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可编辑自己的评论',
  `ability_edit_own_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可编辑自己的回复',
  `ability_edit_own_topic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可编辑自己的话题',
  `ability_delete_own_article` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可删除自己的文章',
  `ability_delete_own_question` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可删除自己的问题',
  `ability_delete_own_answer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可删除自己的回答',
  `ability_delete_own_comment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可删除自己的评论',
  `ability_delete_own_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可删除自己的回复',
  `ability_delete_own_topic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可删除自己的话题',
  `time_before_edit_article` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可编辑自己的文章（单位：分钟，0无限期）',
  `time_before_edit_question` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可编辑自己的问题（单位：分钟，0无限期）',
  `time_before_edit_answer` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可编辑自己的回答（单位：分钟，0无限期）',
  `time_before_edit_comment` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可编辑自己的评论（单位：分钟，0无限期）',
  `time_before_edit_reply` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可编辑自己的回复（单位：分钟，0无限期）',
  `time_before_edit_topic` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可编辑自己的话题（单位：分钟，0无限期）',
  `time_before_delete_article` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可删除自己的文章（单位：分钟，0无限期）',
  `time_before_delete_question` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可删除自己的问题（单位：分钟，0无限期）',
  `time_before_delete_answer` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可删除自己的回答（单位：分钟，0无限期）',
  `time_before_delete_comment` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可删除自己的评论（单位：分钟，0无限期）',
  `time_before_delete_reply` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可删除自己的回复（单位：分钟，0无限期）',
  `time_before_delete_topic` int(10) UNSIGNED NOT NULL DEFAULT '5' COMMENT '在多长时间前可删除自己的话题（单位：分钟，0无限期）',
  `ability_edit_article_only_no_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限文章没有评论的情况下才能编辑',
  `ability_edit_question_only_no_answer` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限问题没有回答的情况下才能编辑',
  `ability_edit_answer_only_no_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限回答没有评论的情况下才能编辑',
  `ability_edit_question_only_no_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限问题没有评论的情况下才能编辑',
  `ability_edit_comment_only_no_reply` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限评论没有回复的情况下才能编辑',
  `ability_edit_reply_only_no_reply` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限回复没有回复的情况下才能编辑',
  `ability_edit_topic_only_no_article_or_question` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限话题没有文章或问题的情况下才能编辑',
  `ability_delete_article_only_no_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限文章没有评论的情况下才能删除',
  `ability_delete_question_only_no_answer` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限问题没有回答的情况下才能删除',
  `ability_delete_answer_only_no_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限回答没有评论的情况下才能删除',
  `ability_delete_question_only_no_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限问题没有评论的情况下才能删除',
  `ability_delete_comment_only_no_reply` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限评论没有回复的情况下才能删除',
  `ability_delete_reply_only_no_reply` tinyint(1) DEFAULT '1' COMMENT '仅限回复没有回复的情况下才能删除',
  `ability_delete_topic_only_no_article_or_question` tinyint(1) NOT NULL DEFAULT '1' COMMENT '仅限话题没有文章或问题的情况下才能删除',
  `ability_edit_own_info` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可编辑自己的个人信息',
  `ability_vote` tinyint(1) DEFAULT '1' COMMENT '能否投票'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `user_group`
--

INSERT INTO `user_group` (`user_group_id`, `user_group_name`, `user_group_description`, `user_group_icon`, `user_group_icon_show`, `user_group_color`, `user_group_user_count`, `create_time`, `update_time`, `delete_time`, `is_admin`, `ability_normal_login`, `ability_admin_login`, `ability_admin_manage_user_group`, `ability_admin_manage_user`, `ability_admin_manage_topic`, `ability_admin_manage_question`, `ability_admin_manage_article`, `ability_admin_manage_comment`, `ability_admin_manage_answer`, `ability_admin_manage_reply`, `ability_admin_manage_report`, `ability_admin_manage_option`, `ability_create_article`, `ability_create_question`, `ability_create_answer`, `ability_create_comment`, `ability_create_reply`, `ability_create_topic`, `ability_edit_own_article`, `ability_edit_own_question`, `ability_edit_own_answer`, `ability_edit_own_comment`, `ability_edit_own_reply`, `ability_edit_own_topic`, `ability_delete_own_article`, `ability_delete_own_question`, `ability_delete_own_answer`, `ability_delete_own_comment`, `ability_delete_own_reply`, `ability_delete_own_topic`, `time_before_edit_article`, `time_before_edit_question`, `time_before_edit_answer`, `time_before_edit_comment`, `time_before_edit_reply`, `time_before_edit_topic`, `time_before_delete_article`, `time_before_delete_question`, `time_before_delete_answer`, `time_before_delete_comment`, `time_before_delete_reply`, `time_before_delete_topic`, `ability_edit_article_only_no_comment`, `ability_edit_question_only_no_answer`, `ability_edit_answer_only_no_comment`, `ability_edit_question_only_no_comment`, `ability_edit_comment_only_no_reply`, `ability_edit_reply_only_no_reply`, `ability_edit_topic_only_no_article_or_question`, `ability_delete_article_only_no_comment`, `ability_delete_question_only_no_answer`, `ability_delete_answer_only_no_comment`, `ability_delete_question_only_no_comment`, `ability_delete_comment_only_no_reply`, `ability_delete_reply_only_no_reply`, `ability_delete_topic_only_no_article_or_question`, `ability_edit_own_info`, `ability_vote`) VALUES
(1, 'Message.Admin.UserGroups.Admin', 'Message.Admin.UserGroups.Admin', 'mdi-security', 1, 'primary', 1, 1702216648, 1725418346, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1),
(2, 'Message.Admin.UserGroups.User', 'Message.Admin.UserGroups.User', 'mdi-account', 0, 'primary', 1, 1702216648, 1746597678, 0, 0, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 5, 5, 5, 5, 5, 5, 5, 0, 5, 5, 5, 5, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `vote`
--

CREATE TABLE `vote` (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `votable_id` int(10) UNSIGNED NOT NULL COMMENT '投票目标ID',
  `votable_type` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '投票目标类型 question、answer、article、comment、reply',
  `type` char(10) NOT NULL COMMENT '投票类型 up、down',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '投票时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`answer_id`);

--
-- 表的索引 `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`article_id`);

--
-- 表的索引 `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`name`);

--
-- 表的索引 `chat_group`
--
ALTER TABLE `chat_group`
  ADD PRIMARY KEY (`chat_group_id`);

--
-- 表的索引 `chat_groupable`
--
ALTER TABLE `chat_groupable`
  ADD PRIMARY KEY (`chat_groupable_id`);

--
-- 表的索引 `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- 表的索引 `domain_data`
--
ALTER TABLE `domain_data`
  ADD PRIMARY KEY (`index_id`);

--
-- 表的索引 `domain_key`
--
ALTER TABLE `domain_key`
  ADD PRIMARY KEY (`index_id`);

--
-- 表的索引 `follow`
--
ALTER TABLE `follow`
  ADD PRIMARY KEY (`follow_id`);

--
-- 表的索引 `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`key`);

--
-- 表的索引 `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`inbox_id`);

--
-- 表的索引 `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- 表的索引 `option`
--
ALTER TABLE `option`
  ADD PRIMARY KEY (`name`);

--
-- 表的索引 `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`question_id`);

--
-- 表的索引 `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`reply_id`);

--
-- 表的索引 `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`);

--
-- 表的索引 `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`token`);

--
-- 表的索引 `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`topic_id`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- 表的索引 `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`user_group_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `answer`
--
ALTER TABLE `answer`
  MODIFY `answer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '回答ID';

--
-- 使用表AUTO_INCREMENT `article`
--
ALTER TABLE `article`
  MODIFY `article_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章ID';

--
-- 使用表AUTO_INCREMENT `chat_group`
--
ALTER TABLE `chat_group`
  MODIFY `chat_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '聊天组ID';

--
-- 使用表AUTO_INCREMENT `chat_groupable`
--
ALTER TABLE `chat_groupable`
  MODIFY `chat_groupable_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '索引ID';

--
-- 使用表AUTO_INCREMENT `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评论ID';

--
-- 使用表AUTO_INCREMENT `domain_data`
--
ALTER TABLE `domain_data`
  MODIFY `index_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '排序';

--
-- 使用表AUTO_INCREMENT `domain_key`
--
ALTER TABLE `domain_key`
  MODIFY `index_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '排序';

--
-- 使用表AUTO_INCREMENT `follow`
--
ALTER TABLE `follow`
  MODIFY `follow_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '关注ID';

--
-- 使用表AUTO_INCREMENT `inbox`
--
ALTER TABLE `inbox`
  MODIFY `inbox_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '私信ID';

--
-- 使用表AUTO_INCREMENT `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知ID';

--
-- 使用表AUTO_INCREMENT `question`
--
ALTER TABLE `question`
  MODIFY `question_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '问题ID';

--
-- 使用表AUTO_INCREMENT `reply`
--
ALTER TABLE `reply`
  MODIFY `reply_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '回复ID';

--
-- 使用表AUTO_INCREMENT `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `topic`
--
ALTER TABLE `topic`
  MODIFY `topic_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '话题ID';

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID', AUTO_INCREMENT=51;

--
-- 使用表AUTO_INCREMENT `user_group`
--
ALTER TABLE `user_group`
  MODIFY `user_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户组ID', AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
