import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Badge from '../../../components/Badge';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';
import ForumPageLayout from '../ForumPageLayout';
import ReactionSummary from './ReactionSummary';

const Show = ({ category, topic, replies, canModerate, canReply, setSidebarOpen }) => {
	const { route } = useRoute();
	const [isEditingTopic, setIsEditingTopic] = useState(false);
	const [replyTo, setReplyTo] = useState(null);
	const rootReplies = replies.filter(reply => reply.parent_id === null);
	const breadcrumbs = [
		...(category.id
			? [
					{
						name: category.title,
						url: route('forum.categories.topics.index', { forum_category: category.id }),
					},
				]
			: []),
		{ name: topic.title, url: route('forum.topics.show', { forum_topic: topic.id }) },
	];

	const removeTopic = () => {
		if (window.confirm('Remove this topic?')) {
			router.delete(route('forum.topics.destroy', { forum_topic: topic.id }));
		}
	};

	const restoreTopic = () => router.post(route('forum.topics.restore', { forum_topic: topic.id }));
	const toggleLock = () => router.put(route('forum.topics.lock', { forum_topic: topic.id }));
	const togglePin = () => router.put(route('forum.topics.pin', { forum_topic: topic.id }));

	return (
		<>
			<AppHead title={topic.title} />
			<ForumPageLayout
				title={topic.title}
				description={`${category.title ? `${category.title} · ` : ''}Started by ${topic.author_name} · ${new Date(topic.created_at).toLocaleString()}`}
				breadcrumbs={breadcrumbs}
				setSidebarOpen={setSidebarOpen}
			>
				<div className="mx-auto max-w-5xl space-y-8">
					<article className="rounded-lg border border-gray-200 bg-white shadow-sm">
						{isEditingTopic ? (
							<div className="p-5 sm:p-6">
								<TopicEditor topic={topic} onCancel={() => setIsEditingTopic(false)} />
							</div>
						) : (
						<>
							<div className="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4 sm:px-6">
								<div className="flex flex-wrap gap-2">
									{topic.is_pinned && <Badge colour="bg-purple-100 text-purple-800">Pinned</Badge>}
									{topic.is_locked && <Badge colour="bg-amber-100 text-amber-800">Locked</Badge>}
									{topic.is_deleted && <Badge colour="bg-red-100 text-red-800">Removed</Badge>}
								</div>
								{(topic.can_edit || canModerate) && !topic.is_deleted && (
									<div className="flex flex-wrap items-center gap-1">
										{topic.can_edit && (
											<Button weight="clear" size="sm" onClick={() => setIsEditingTopic(true)}>
												<Icon icon="pencil" /> Edit
											</Button>
										)}
										{topic.can_edit && (
											<Button intent="danger" weight="clear" size="sm" onClick={removeTopic}>
												<Icon icon="trash" /> Remove
											</Button>
										)}
										{canModerate && (
											<>
												<Button weight="clear" size="sm" onClick={toggleLock}>
													<Icon icon={topic.is_locked ? 'lock-open' : 'lock'} />
													{topic.is_locked ? 'Unlock' : 'Lock'}
												</Button>
												<Button weight="clear" size="sm" onClick={togglePin}>
													<Icon icon="thumbtack" />
													{topic.is_pinned ? 'Unpin' : 'Pin'}
												</Button>
											</>
										)}
									</div>
								)}
								{canModerate && topic.is_deleted && (
									<Button intent="primary" weight="outline" size="sm" onClick={restoreTopic}>
										<Icon icon="undo" /> Restore topic
									</Button>
								)}
							</div>

							<div className="px-5 py-6 sm:px-6">
								<Prose content={topic.body_html} className="text-base text-gray-800" />
								<div className="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
									{!topic.is_deleted && (
										<Button
											intent={topic.is_following ? 'secondary' : 'primary'}
											weight={topic.is_following ? 'outline' : 'solid'}
											onClick={() =>
												topic.is_following
													? router.delete(route('forum.topics.unfollow', { forum_topic: topic.id }), { preserveScroll: true })
													: router.put(route('forum.topics.follow', { forum_topic: topic.id }), {}, { preserveScroll: true })
											}
										>
											<Icon icon="bookmark" />
											{topic.is_following ? 'Unfollow topic' : 'Follow topic'}
										</Button>
									)}
									{!topic.is_deleted && (
										<ReactionBar targetType="topics" targetId={topic.id} reactions={topic.reactions} />
									)}
								</div>
							</div>
						</>
						)}
					</article>

					<section className="space-y-4">
						<div className="flex items-center justify-between gap-3">
							<h2 className="text-xl font-bold text-gray-900">Replies</h2>
							<Badge colour="bg-gray-200">{replies.filter(reply => !reply.is_deleted).length}</Badge>
						</div>
						{rootReplies.map(reply => {
							const nestedReplies = replies.filter(nestedReply => nestedReply.parent_id === reply.id);

							return (
								<div key={reply.id} className="space-y-3">
									<ReplyCard
										reply={reply}
										canModerate={canModerate}
										onReply={() => setReplyTo(reply.id)}
									/>
									{replyTo === reply.id && canReply && (
										<ReplyComposer
											topicId={topic.id}
											parentId={reply.id}
											onCancel={() => setReplyTo(null)}
										/>
									)}
									{nestedReplies.length > 0 && (
										<div className="ml-4 space-y-3 border-l-2 border-purple-100 pl-4 sm:ml-10">
											{nestedReplies.map(nestedReply => (
												<ReplyCard key={nestedReply.id} reply={nestedReply} canModerate={canModerate} />
											))}
										</div>
									)}
								</div>
							);
						})}
						{rootReplies.length === 0 && (
							<div className="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-600">
								No replies yet. Start the conversation below.
							</div>
						)}
					</section>

					{canReply && !replyTo && <ReplyComposer topicId={topic.id} parentId={null} />}
					{!canReply && topic.is_locked && (
						<p className="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
							This topic is locked.
						</p>
					)}
				</div>
			</ForumPageLayout>
		</>
	);
};

const TopicEditor = ({ topic, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, put, processing, errors } = useForm({ title: topic.title, body: topic.body ?? '' });

	const submit = event => {
		event.preventDefault();
		put(route('forum.topics.update', { forum_topic: topic.id }), {
			preserveScroll: true,
			onSuccess: onCancel,
		});
	};

	return (
		<form onSubmit={submit} className="space-y-5">
			<h2 className="text-lg font-semibold text-gray-900">Edit topic</h2>
			<div>
				<label htmlFor="edit-topic-title" className="mb-1 block text-sm font-medium text-gray-700">
					Title
				</label>
				<input
					id="edit-topic-title"
					value={data.title}
					onChange={event => setData('title', event.target.value)}
					className="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={255}
					required
				/>
				{errors.title && <p className="mt-1 text-sm text-red-700">{errors.title}</p>}
			</div>
			<div>
				<label htmlFor="edit-topic-body" className="mb-1 block text-sm font-medium text-gray-700">
					Message (Markdown supported)
				</label>
				<textarea
					id="edit-topic-body"
					value={data.body}
					onChange={event => setData('body', event.target.value)}
					className="block min-h-40 w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={20000}
					required
				/>
				{errors.body && <p className="mt-1 text-sm text-red-700">{errors.body}</p>}
			</div>
			<div className="flex justify-end gap-2">
				<Button weight="clear" onClick={onCancel}>
					Cancel
				</Button>
				<Button type="submit" intent="primary" disabled={processing}>
					Save changes
				</Button>
			</div>
		</form>
	);
};

const ReplyCard = ({ reply, canModerate, onReply }) => {
	const { route } = useRoute();
	const [isEditing, setIsEditing] = useState(false);

	const removeReply = () => {
		if (window.confirm('Remove this reply?')) {
			router.delete(route('forum.replies.destroy', { forum_reply: reply.id }), { preserveScroll: true });
		}
	};

	const restoreReply = () =>
		router.post(route('forum.replies.restore', { forum_reply: reply.id }), {}, { preserveScroll: true });

	return (
		<article
			className={`rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6 ${
				reply.is_deleted ? 'border-dashed bg-gray-50 opacity-75' : ''
			}`}
		>
			{isEditing ? (
				<ReplyEditor reply={reply} onCancel={() => setIsEditing(false)} />
			) : (
				<>
					<div className="flex items-start gap-3">
						<div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-semibold text-purple-800">
							{reply.author_name?.charAt(0)?.toUpperCase()}
						</div>
						<div className="min-w-0 flex-1">
							<div className="flex flex-wrap items-center gap-x-2 gap-y-1">
								<p className="text-sm font-semibold text-gray-900">{reply.author_name}</p>
								<time dateTime={reply.created_at} className="text-xs text-gray-500">
									{new Date(reply.created_at).toLocaleString()}
								</time>
								{reply.is_deleted && <Badge colour="bg-gray-200">Removed</Badge>}
							</div>
							<Prose content={reply.body_html} className="mt-3 text-sm text-gray-800" />
							{!reply.is_deleted && (
								<ReactionBar targetType="replies" targetId={reply.id} reactions={reply.reactions} />
							)}
						</div>
					</div>
					<div className="mt-4 flex flex-wrap items-center justify-end gap-1 border-t border-gray-100 pt-3">
						{onReply && !reply.is_deleted && (
							<Button size="sm" weight="clear" onClick={onReply}>
								<Icon icon="reply" /> Reply
							</Button>
						)}
						{reply.can_edit && !reply.is_deleted && (
							<Button size="sm" weight="clear" onClick={() => setIsEditing(true)}>
								<Icon icon="pencil" /> Edit
							</Button>
						)}
						{reply.can_edit && !reply.is_deleted && (
							<Button intent="danger" size="sm" weight="clear" onClick={removeReply}>
								<Icon icon="trash" /> Remove
							</Button>
						)}
						{canModerate && reply.can_restore && (
							<Button size="sm" weight="clear" onClick={restoreReply}>
								<Icon icon="undo" /> Restore
							</Button>
						)}
					</div>
				</>
			)}
		</article>
	);
};

const ReactionBar = ({ targetType, targetId, reactions }) => {
	const { route } = useRoute();
	const availableEmojis = ['👍', '❤️', '😂', '🎉', '👀'];
	const reactionRoute = `forum.${targetType}.reactions`;
	const toggleReaction = emoji => {
		const existingReaction = reactions.find(reaction => reaction.emoji === emoji && reaction.reacted);
		const url = route(`${reactionRoute}.${existingReaction ? 'destroy' : 'store'}`, {
			[targetType === 'topics' ? 'forum_topic' : 'forum_reply']: targetId,
		});

		if (existingReaction) {
			router.delete(url, { data: { emoji }, preserveScroll: true });
		} else {
			router.post(url, { emoji }, { preserveScroll: true });
		}
	};

	return (
		<div className="mt-3">
			<ReactionSummary reactions={reactions} availableEmojis={availableEmojis} onReaction={toggleReaction} />
		</div>
	);
};

const ReplyEditor = ({ reply, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, put, processing, errors } = useForm({ body: reply.body ?? '' });

	const submit = event => {
		event.preventDefault();
		put(route('forum.replies.update', { forum_reply: reply.id }), {
			preserveScroll: true,
			onSuccess: onCancel,
		});
	};

	return (
		<form onSubmit={submit} className="space-y-4">
			<label htmlFor={`edit-reply-${reply.id}`} className="block text-sm font-medium text-gray-700">
				Edit reply (Markdown supported)
			</label>
			<textarea
				id={`edit-reply-${reply.id}`}
				value={data.body}
				onChange={event => setData('body', event.target.value)}
				className="block min-h-32 w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
				maxLength={20000}
				required
			/>
			{errors.body && <p className="text-sm text-red-700">{errors.body}</p>}
			<div className="flex justify-end gap-2">
				<Button weight="clear" onClick={onCancel}>
					Cancel
				</Button>
				<Button type="submit" intent="primary" disabled={processing}>
					Save reply
				</Button>
			</div>
		</form>
	);
};

const ReplyComposer = ({ topicId, parentId, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors } = useForm({ body: '', parent_id: parentId });

	const submit = event => {
		event.preventDefault();
		post(route('forum.topics.replies.store', { forum_topic: topicId }), {
			preserveScroll: true,
			onSuccess: () => {
				setData('body', '');
				onCancel?.();
			},
		});
	};

	return (
		<form onSubmit={submit} className="space-y-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
			<label htmlFor={`reply-body-${parentId ?? 'topic'}`} className="block text-sm font-medium text-gray-700">
				{parentId ? 'Reply to this message (Markdown supported)' : 'Add a reply (Markdown supported)'}
			</label>
			<textarea
				id={`reply-body-${parentId ?? 'topic'}`}
				value={data.body}
				onChange={event => setData('body', event.target.value)}
				className="block min-h-32 w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
				maxLength={20000}
				required
			/>
			{errors.body && <p className="text-sm text-red-700">{errors.body}</p>}
			{errors.parent_id && <p className="text-sm text-red-700">{errors.parent_id}</p>}
			{errors.topic && <p className="text-sm text-red-700">{errors.topic}</p>}
			<div className="flex justify-end gap-2">
				{onCancel && (
					<Button weight="clear" onClick={onCancel}>
						Cancel
					</Button>
				)}
				<Button type="submit" intent="primary" disabled={processing}>
					Post reply
				</Button>
			</div>
		</form>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;
