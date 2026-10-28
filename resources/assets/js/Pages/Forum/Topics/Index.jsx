import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';

const Index = ({ category, topics, canCreateTopic }) => {
	const { route } = useRoute();
	const [isCreating, setIsCreating] = useState(false);

	return (
		<>
			<AppHead title={category.title} />
			<div className="mx-auto max-w-4xl px-4 py-8">
				<div className="mb-6 flex flex-wrap items-start justify-between gap-4">
					<div>
						<Link
							href={route('forum.categories.index')}
							className="text-sm font-semibold text-purple-700 hover:text-purple-900"
						>
							Forum Categories
						</Link>
						<h1 className="mt-2 text-2xl font-bold text-gray-900">{category.title}</h1>
						<p className="mt-1 text-sm text-gray-600">Discussions in this category.</p>
					</div>
					{canCreateTopic && !isCreating && (
						<button
							type="button"
							className="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800"
							onClick={() => setIsCreating(true)}
						>
							Start a discussion
						</button>
					)}
				</div>

				{isCreating && <TopicForm categoryId={category.id} onCancel={() => setIsCreating(false)} />}

				<div className="space-y-3">
					{topics.map(topic => (
						<article key={topic.id} className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
							<div className="flex flex-wrap items-start justify-between gap-2">
								<div>
									<Link
										href={route('forum.topics.show', { forum_topic: topic.id })}
										className="text-lg font-semibold text-gray-900 hover:text-purple-800"
									>
										{topic.title}
									</Link>
									{topic.is_pinned && (
										<span className="ml-2 rounded bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800">
											Pinned
										</span>
									)}
								</div>
								<span className="text-sm text-gray-600">
									{topic.replies_count} {topic.replies_count === 1 ? 'reply' : 'replies'}
								</span>
							</div>
							<Prose content={topic.body_html} className="mt-3 text-sm text-gray-700" />
							<p className="mt-3 text-xs text-gray-500">
								Started by {topic.author_name} · {new Date(topic.created_at).toLocaleString()}
							</p>
						</article>
					))}
					{topics.length === 0 && (
						<p className="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-600">
							There are no discussions in this category yet.
						</p>
					)}
				</div>
			</div>
		</>
	);
};

const TopicForm = ({ categoryId, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors } = useForm({ title: '', body: '' });

	const submit = event => {
		event.preventDefault();
		post(route('forum.categories.topics.store', { forum_category: categoryId }), {
			preserveScroll: true,
			onSuccess: onCancel,
		});
	};

	return (
		<form onSubmit={submit} className="mb-6 space-y-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
			<h2 className="text-lg font-semibold text-gray-900">Start a discussion</h2>
			<div>
				<label htmlFor="topic-title" className="mb-1 block text-sm font-medium text-gray-700">
					Title
				</label>
				<input
					id="topic-title"
					value={data.title}
					onChange={event => setData('title', event.target.value)}
					className="w-full rounded border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={255}
					required
				/>
				{errors.title && <p className="mt-1 text-sm text-red-700">{errors.title}</p>}
			</div>
			<div>
				<label htmlFor="topic-body" className="mb-1 block text-sm font-medium text-gray-700">
					Message (Markdown supported)
				</label>
				<textarea
					id="topic-body"
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
					className="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-50"
					disabled={processing}
				>
					{processing ? 'Posting…' : 'Post discussion'}
				</button>
			</div>
		</form>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
