import React, { useState } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';

const Show = ({ category, topic, replies, canModerate, canReply }) => {
	const { route } = useRoute();
	const [isEditingTopic, setIsEditingTopic] = useState(false);
	const [replyTo, setReplyTo] = useState(null);
	const rootReplies = replies.filter(reply => reply.parent_id === null);

	const removeTopic = () => {
		if (window.confirm('Remove this discussion?')) {
			router.delete(route('forum.topics.destroy', { forum_topic: topic.id }));
		}
	};

	const restoreTopic = () => router.post(route('forum.topics.restore', { forum_topic: topic.id }));
	const toggleLock = () => router.put(route('forum.topics.lock', { forum_topic: topic.id }));
	const togglePin = () => router.put(route('forum.topics.pin', { forum_topic: topic.id }));

	return (
		<>
			<AppHead title={topic.title} />
			<div className="mx-auto max-w-4xl px-4 py-8">
				<Link
					href={
						category.id
							? route('forum.categories.topics.index', { forum_category: category.id })
							: route('forum.categories.index')
					}
					className="text-sm font-semibold text-purple-700 hover:text-purple-900"
				>
					{category.title ?? 'Forum Categories'}
				</Link>

				<article className="mt-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
					{isEditingTopic ? (
						<TopicEditor topic={topic} onCancel={() => setIsEditingTopic(false)} />
					) : (
						<>
							<div className="flex flex-wrap items-start justify-between gap-3">
								<div>
									<h1 className="text-2xl font-bold text-gray-900">{topic.title}</h1>
									<div className="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
										{topic.is_pinned && <StatusBadge>Pinned</StatusBadge>}
										{topic.is_locked && <StatusBadge>Locked</StatusBadge>}
										{topic.is_deleted && <StatusBadge>Removed</StatusBadge>}
									</div>
								</div>
								{(topic.can_edit || canModerate) && !topic.is_deleted && (
									<div className="flex flex-wrap gap-3 text-sm">
										{topic.can_edit && (
											<button
												type="button"
												className="font-semibold text-purple-700 hover:text-purple-900"
												onClick={() => setIsEditingTopic(true)}
											>
												Edit
											</button>
										)}
										{topic.can_edit && (
											<button
												type="button"
												className="font-semibold text-red-700 hover:text-red-900"
												onClick={removeTopic}
											>
												Remove
											</button>
										)}
										{canModerate && (
											<>
												<button
													type="button"
													className="font-semibold text-gray-700 hover:text-gray-900"
													onClick={toggleLock}
												>
													{topic.is_locked ? 'Unlock' : 'Lock'}
												</button>
												<button
													type="button"
													className="font-semibold text-gray-700 hover:text-gray-900"
													onClick={togglePin}
												>
													{topic.is_pinned ? 'Unpin' : 'Pin'}
												</button>
											</>
										)}
									</div>
								)}
								{canModerate && topic.is_deleted && (
									<button
										type="button"
										className="text-sm font-semibold text-purple-700 hover:text-purple-900"
										onClick={restoreTopic}
									>
										Restore discussion
									</button>
								)}
							</div>
							<Prose content={topic.body_html} className="mt-4 text-sm text-gray-800" />
							<p className="mt-4 text-xs text-gray-500">
								Started by {topic.author_name} · {new Date(topic.created_at).toLocaleString()}
							</p>
						</>
					)}
				</article>

				<section className="mt-8 space-y-4">
					<h2 className="text-xl font-bold text-gray-900">
						Replies ({replies.filter(reply => !reply.is_deleted).length})
					</h2>
					{rootReplies.map(reply => {
						const nestedReplies = replies.filter(nestedReply => nestedReply.parent_id === reply.id);

						return (
							<div key={reply.id}>
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
								{nestedReplies.map(nestedReply => (
									<div
										key={nestedReply.id}
										className="ml-6 mt-3 border-l-2 border-gray-200 pl-4 sm:ml-10"
									>
										<ReplyCard reply={nestedReply} canModerate={canModerate} />
									</div>
								))}
							</div>
						);
					})}
					{rootReplies.length === 0 && <p className="text-sm text-gray-600">No replies yet.</p>}
				</section>

				{canReply && !replyTo && <ReplyComposer topicId={topic.id} parentId={null} />}
				{!canReply && topic.is_locked && (
					<p className="mt-6 text-sm text-gray-600">This discussion is locked.</p>
				)}
			</div>
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
		<form onSubmit={submit} className="space-y-4">
			<h2 className="text-lg font-semibold text-gray-900">Edit discussion</h2>
			<div>
				<label htmlFor="edit-topic-title" className="mb-1 block text-sm font-medium text-gray-700">
					Title
				</label>
				<input
					id="edit-topic-title"
					value={data.title}
					onChange={event => setData('title', event.target.value)}
					className="w-full rounded border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
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
					className="min-h-40 w-full rounded border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={20000}
					required
				/>
				{errors.body && <p className="mt-1 text-sm text-red-700">{errors.body}</p>}
			</div>
			<div className="flex justify-end gap-2">
				<button
					type="button"
					className="rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
					onClick={onCancel}
				>
					Cancel
				</button>
				<button
					type="submit"
					className="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
					disabled={processing}
				>
					Save changes
				</button>
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
			className={`rounded-lg border border-gray-200 bg-white p-4 shadow-sm ${
				reply.is_deleted ? 'border-dashed opacity-75' : ''
			}`}
		>
			{isEditing ? (
				<ReplyEditor reply={reply} onCancel={() => setIsEditing(false)} />
			) : (
				<>
					<Prose content={reply.body_html} className="text-sm text-gray-800" />
					<div className="mt-3 flex flex-wrap items-center justify-between gap-2">
						<p className="text-xs text-gray-500">
							{reply.author_name} · {new Date(reply.created_at).toLocaleString()}
						</p>
						<div className="flex flex-wrap gap-3 text-xs font-semibold">
							{onReply && !reply.is_deleted && (
								<button
									type="button"
									className="text-purple-700 hover:text-purple-900"
									onClick={onReply}
								>
									Reply
								</button>
							)}
							{reply.can_edit && !reply.is_deleted && (
								<button
									type="button"
									className="text-purple-700 hover:text-purple-900"
									onClick={() => setIsEditing(true)}
								>
									Edit
								</button>
							)}
							{reply.can_edit && !reply.is_deleted && (
								<button type="button" className="text-red-700 hover:text-red-900" onClick={removeReply}>
									Remove
								</button>
							)}
							{canModerate && reply.can_restore && (
								<button
									type="button"
									className="text-purple-700 hover:text-purple-900"
									onClick={restoreReply}
								>
									Restore
								</button>
							)}
						</div>
					</div>
				</>
			)}
		</article>
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
		<form onSubmit={submit} className="space-y-3">
			<label htmlFor={`edit-reply-${reply.id}`} className="block text-sm font-medium text-gray-700">
				Edit reply (Markdown supported)
			</label>
			<textarea
				id={`edit-reply-${reply.id}`}
				value={data.body}
				onChange={event => setData('body', event.target.value)}
				className="min-h-32 w-full rounded border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
				maxLength={20000}
				required
			/>
			{errors.body && <p className="text-sm text-red-700">{errors.body}</p>}
			<div className="flex justify-end gap-2">
				<button
					type="button"
					className="rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
					onClick={onCancel}
				>
					Cancel
				</button>
				<button
					type="submit"
					className="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
					disabled={processing}
				>
					Save reply
				</button>
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
		<form onSubmit={submit} className="mt-4 space-y-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
			<label htmlFor={`reply-body-${parentId ?? 'topic'}`} className="block text-sm font-medium text-gray-700">
				{parentId ? 'Reply to this message (Markdown supported)' : 'Add a reply (Markdown supported)'}
			</label>
			<textarea
				id={`reply-body-${parentId ?? 'topic'}`}
				value={data.body}
				onChange={event => setData('body', event.target.value)}
				className="min-h-32 w-full rounded border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
				maxLength={20000}
				required
			/>
			{errors.body && <p className="text-sm text-red-700">{errors.body}</p>}
			{errors.parent_id && <p className="text-sm text-red-700">{errors.parent_id}</p>}
			{errors.topic && <p className="text-sm text-red-700">{errors.topic}</p>}
			<div className="flex justify-end gap-2">
				{onCancel && (
					<button
						type="button"
						className="rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
						onClick={onCancel}
					>
						Cancel
					</button>
				)}
				<button
					type="submit"
					className="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
					disabled={processing}
				>
					Post reply
				</button>
			</div>
		</form>
	);
};

const StatusBadge = ({ children }) => <span className="rounded bg-gray-100 px-2 py-1 text-gray-700">{children}</span>;

Show.layout = page => <TenantLayout children={page} />;

export default Show;
