import React, { useState } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Badge from '../../../components/Badge';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';
import ChannelBadge from '../ChannelBadge';
import formatRelativeDate from '../formatRelativeDate';
import MemberLink from '../MemberLink';
import ReactionSummary from './ReactionSummary';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';

const Show = ({ category, topic, replies, canModerate, canReply, setSidebarOpen }) => {
	const { route } = useRoute();
	const [isEditingTopic, setIsEditingTopic] = useState(false);
	const [replyTo, setReplyTo] = useState(null);
	const rootReplies = replies.filter(reply => reply.parent_id === null);
	const breadcrumbs = [
		{ name: 'Discussions', url: route('forum.topics.index') },
		...(category.id
			? [
					{
						name: `# ${category.title}`,
						url: route('forum.categories.topics.index', { forum_category: category.id }),
					},
			  ]
			: []),
		{ name: topic.title, url: route('forum.topics.show', { forum_topic: topic.id }) },
	];

	const removeTopic = () => {
		if (window.confirm('Remove this thread?')) {
			router.delete(route('forum.topics.destroy', { forum_topic: topic.id }));
		}
	};

	const restoreTopic = () => router.post(route('forum.topics.restore', { forum_topic: topic.id }));
	const toggleLock = () => router.put(route('forum.topics.lock', { forum_topic: topic.id }));
	const togglePin = () => router.put(route('forum.topics.pin', { forum_topic: topic.id }));
	const toggleFollow = () =>
		topic.is_following
			? router.delete(route('forum.topics.unfollow', { forum_topic: topic.id }), { preserveScroll: true })
			: router.put(route('forum.topics.follow', { forum_topic: topic.id }), {}, { preserveScroll: true });
	const actions = [
		...(topic.can_edit && !topic.is_deleted && !isEditingTopic
			? [{ label: 'Edit', icon: 'pencil', intent: 'secondary', onClick: () => setIsEditingTopic(true) }]
			: []),
		...(topic.can_edit && !topic.is_deleted
			? [{ label: 'Remove', icon: 'trash', intent: 'danger', weight: 'outline', onClick: removeTopic }]
			: []),
		...(canModerate && !topic.is_deleted
			? [
					{
						label: topic.is_locked ? 'Unlock' : 'Lock',
						icon: topic.is_locked ? 'lock-open' : 'lock',
						intent: topic.is_locked ? 'primary' : 'secondary',
						onClick: toggleLock,
					},
					{
						label: topic.is_pinned ? 'Unpin' : 'Pin',
						icon: 'thumbtack',
						intent: topic.is_pinned ? 'primary' : 'secondary',
						onClick: togglePin,
					},
			  ]
			: []),
		...(canModerate && topic.is_deleted ? [{ label: 'Restore thread', icon: 'undo', onClick: restoreTopic }] : []),
	];

	return (
		<>
			<AppHead title={topic.title} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Discussions" breadcrumbs={breadcrumbs}>
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem
								key={action.label}
								url={action.url}
								onClick={action.onClick}
								method={action.method}
								intent={action.intent}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<div className="flex w-full items-start justify-between gap-4">
					<PageHeaderContent>
						<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
						<PageHeaderTitle>
							<span className="flex min-w-0 items-center gap-2">
								{topic.is_pinned && <Icon icon="thumbtack" className="shrink-0 text-purple-700" />}
								<span className="min-w-0">{topic.title}</span>
								{topic.is_locked && <Badge colour="bg-amber-100 text-amber-800">Locked</Badge>}
								{topic.is_deleted && <Badge colour="bg-red-100 text-red-800">Removed</Badge>}
							</span>
						</PageHeaderTitle>
						<PageHeaderMeta>
							<div className="inline-flex flex-wrap items-center gap-2">
								{topic.author_avatar_url && (
									<img
										src={topic.author_avatar_url}
										alt=""
										className="h-6 w-6 rounded-full object-cover"
									/>
								)}
								<MemberLink membershipId={topic.author_membership_id}>{topic.author_name}</MemberLink>
								{category.id && category.title && (
									<>
										<span aria-hidden="true">
											<Icon icon="chevron-right" size="text-[10px]" className="text-gray-400" />
										</span>
										<Link
											href={route('forum.categories.topics.index', {
												forum_category: category.id,
											})}
										>
											<ChannelBadge name={category.title} />
										</Link>
									</>
								)}
								<span aria-hidden="true">·</span>
								<time dateTime={topic.created_at}>{formatRelativeDate(topic.created_at)}</time>
							</div>
						</PageHeaderMeta>
						{isEditingTopic ? (
							<TopicEditor topic={topic} onCancel={() => setIsEditingTopic(false)} />
						) : (
							<>
								<Prose content={topic.body_html} className="mt-4 text-base text-gray-800" />
								{!topic.is_deleted && (
									<div className="border-t border-gray-100 pt-4">
										<ReactionBar
											targetType="topics"
											targetId={topic.id}
											reactions={topic.reactions}
										/>
									</div>
								)}
							</>
						)}
					</PageHeaderContent>
					<div className="flex shrink-0 items-start gap-3 xl:flex-col xl:items-end xl:self-stretch">
						{!topic.is_deleted && (
							<Button
								aria-label={topic.is_following ? 'Unfollow thread' : 'Follow thread'}
								onClick={toggleFollow}
								size="lg"
								intent="primary"
								weight="clear"
							>
								<Icon icon="bookmark" type={topic.is_following ? 'solid' : 'regular'} />
							</Button>
						)}
						<div className="xl:mt-auto">
							<PageHeaderActions>
								{actions.map(action => (
									<Button
										key={action.label}
										href={action.url}
										onClick={action.onClick}
										method={action.method}
										size="md"
										intent={action.intent ?? 'primary'}
										weight={action.weight ?? 'solid'}
									>
										<Icon icon={action.icon} mr />
										{action.label}
									</Button>
								))}
							</PageHeaderActions>
						</div>
					</div>
				</div>
			</PageHeader>

			<main className="min-h-0 flex-1 bg-gray-100 px-4 py-6 sm:px-6 md:px-8">
				<div className="w-full max-w-screen-2xl">
					<div className="w-full max-w-5xl space-y-8">
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
													<ReplyCard
														key={nestedReply.id}
														reply={nestedReply}
														canModerate={canModerate}
													/>
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
								This thread is locked.
							</p>
						)}
					</div>
				</div>
			</main>
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
			<h2 className="text-lg font-semibold text-gray-900">Edit thread</h2>
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
								<p className="text-sm font-semibold">
									<MemberLink
										membershipId={reply.author_membership_id}
										className="text-sm font-semibold"
									>
										{reply.author_name}
									</MemberLink>
								</p>
								<time dateTime={reply.created_at} className="text-xs text-gray-500">
									{formatRelativeDate(reply.created_at)}
								</time>
								{reply.is_deleted && <Badge colour="bg-gray-200">Removed</Badge>}
							</div>
							<Prose content={reply.body_html} className="mt-3 text-sm text-gray-800" />
						</div>
					</div>
					<div className="mt-4 flex flex-col gap-3 border-t border-gray-100 pt-3 md:flex-row md:items-center md:justify-between">
						{!reply.is_deleted && (
							<ReactionBar targetType="replies" targetId={reply.id} reactions={reply.reactions} />
						)}
						<div className="flex flex-wrap items-center justify-start gap-1">
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
							{canModerate && reply.is_deleted && reply.can_restore && (
								<Button size="sm" weight="clear" onClick={restoreReply}>
									<Icon icon="undo" /> Restore
								</Button>
							)}
						</div>
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

	return <ReactionSummary reactions={reactions} availableEmojis={availableEmojis} onReaction={toggleReaction} />;
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
