import React from 'react';
import { Link } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';

const Following = ({ topics }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Followed discussions" />
			<div className="mx-auto max-w-4xl px-4 py-8">
				<div className="mb-6">
					<Link href={route('forum.categories.index')} className="text-sm font-semibold text-purple-700 hover:text-purple-900">
						Forum Categories
					</Link>
					<h1 className="mt-2 text-2xl font-bold text-gray-900">Followed discussions</h1>
				</div>
				<div className="space-y-3">
					{topics.map(topic => (
						<article key={topic.id} className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
							<Link href={route('forum.topics.show', { forum_topic: topic.id })} className="text-lg font-semibold text-gray-900 hover:text-purple-800">
								{topic.title}
							</Link>
							<p className="mt-2 text-xs text-gray-500">
								{topic.category_title} · {topic.author_name} · {new Date(topic.created_at).toLocaleString()}
							</p>
						</article>
					))}
					{topics.length === 0 && <p className="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-600">You are not following any discussions.</p>}
				</div>
			</div>
		</>
	);
};

Following.layout = page => <TenantLayout children={page} />;

export default Following;
