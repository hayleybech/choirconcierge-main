import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';

const Index = ({ categories, ensembles, canManageCategories }) => {
	const { route } = useRoute();
	const [activeForm, setActiveForm] = useState(null);
	const formCategory = activeForm === 'new' ? null : activeForm;

	const deleteCategory = category => {
		if (window.confirm(`Delete the "${category.title}" category?`)) {
			router.delete(route('forum.categories.destroy', { forum_category: category.id }), {
				preserveScroll: true,
			});
		}
	};

	return (
		<>
			<AppHead title="Forum Categories" />
			<div className="mx-auto max-w-4xl px-4 py-8">
				<div className="mb-6 flex items-center justify-between gap-4">
					<div>
						<h1 className="text-2xl font-bold text-gray-900">Forum Categories</h1>
						<p className="mt-1 text-sm text-gray-600">
							Categories help members find conversations relevant to their choir.
						</p>
					</div>
					{canManageCategories && !activeForm && (
						<button
							type="button"
							className="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800"
							onClick={() => setActiveForm('new')}
						>
							Add Category
						</button>
					)}
				</div>

				{activeForm && (
					<CategoryForm
						key={formCategory?.id ?? 'new'}
						category={formCategory}
						ensembles={ensembles}
						onCancel={() => setActiveForm(null)}
					/>
				)}

				<div className="space-y-3">
					{categories.map(category => (
						<article key={category.id} className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
							<div className="flex items-start justify-between gap-4">
								<div>
									<h2 className="font-semibold text-gray-900">{category.title}</h2>
									<p className="mt-1 text-sm text-gray-600">
										{category.is_choir_wide
											? 'Visible to all active choir members'
											: category.ensembles.length > 0
												? `Visible to ${category.ensembles.map(ensemble => ensemble.name).join(', ')}`
												: 'Restricted to ensembles that are no longer available'}
									</p>
								</div>
								{canManageCategories && (
									<div className="flex shrink-0 gap-3 text-sm">
										<button
											type="button"
											className="font-semibold text-purple-700 hover:text-purple-900"
											onClick={() => setActiveForm(category)}
										>
											Edit
										</button>
										<button
											type="button"
											className="font-semibold text-red-700 hover:text-red-900"
											onClick={() => deleteCategory(category)}
										>
											Delete
										</button>
									</div>
								)}
							</div>
						</article>
					))}
					{categories.length === 0 && (
						<p className="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-600">
							There are no forum categories available to you yet.
						</p>
					)}
				</div>
			</div>
		</>
	);
};

const CategoryForm = ({ category, ensembles, onCancel }) => {
	const { route } = useRoute();
	const { data, setData, post, put, processing, errors } = useForm({
		title: category?.title ?? '',
		is_choir_wide: category?.is_choir_wide ?? true,
		ensemble_ids: category?.ensembles?.map(ensemble => ensemble.id) ?? [],
	});

	const submit = event => {
		event.preventDefault();
		const options = { preserveScroll: true, onSuccess: onCancel };

		if (category) {
			put(route('forum.categories.update', { forum_category: category.id }), options);
			return;
		}

		post(route('forum.categories.store'), options);
	};

	return (
		<form onSubmit={submit} className="mb-6 space-y-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
			<h2 className="text-lg font-semibold text-gray-900">
				{category ? 'Edit Forum Category' : 'Create Forum Category'}
			</h2>
			<div>
				<label htmlFor="forum-category-title" className="mb-1 block text-sm font-medium text-gray-700">
					Category name
				</label>
				<input
					id="forum-category-title"
					name="title"
					value={data.title}
					onChange={event => setData('title', event.target.value)}
					className="w-full rounded border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
					maxLength={255}
					required
				/>
				{errors.title && <p className="mt-1 text-sm text-red-700">{errors.title}</p>}
			</div>
			<div>
				<label className="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
					<input
						type="checkbox"
						checked={data.is_choir_wide}
						onChange={event => setData('is_choir_wide', event.target.checked)}
						className="rounded border-gray-300 text-purple-700 focus:ring-purple-500"
					/>
					Available to all active choir members
				</label>
				<select
					id="forum-category-ensembles"
					name="ensemble_ids"
					multiple
					value={data.ensemble_ids.map(String)}
					disabled={data.is_choir_wide}
					onChange={event =>
						setData(
							'ensemble_ids',
							Array.from(event.target.selectedOptions, option => Number(option.value))
						)
					}
					className="w-full rounded border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
				>
					{ensembles.map(ensemble => (
						<option key={ensemble.id} value={ensemble.id}>
							{ensemble.name}
						</option>
					))}
				</select>
				<p className="mt-1 text-xs text-gray-500">Uncheck the box to restrict this category to selected ensembles.</p>
				{(errors.ensemble_ids || errors['ensemble_ids.0']) && (
					<p className="mt-1 text-sm text-red-700">{errors.ensemble_ids || errors['ensemble_ids.0']}</p>
				)}
			</div>
			<div className="flex justify-end gap-2">
				<button type="button" className="rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100" onClick={onCancel}>
					Cancel
				</button>
				<button
					type="submit"
					className="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-50"
					disabled={processing}
				>
					{processing ? 'Saving…' : 'Save Category'}
				</button>
			</div>
		</form>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;