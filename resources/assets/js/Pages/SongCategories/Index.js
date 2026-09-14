import React, { useEffect, useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import SongCategoryTableDesktop from './SongCategoryTableDesktop';
import SongCategoryTableMobile from './SongCategoryTableMobile';
import { useForm, usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';
import Dialog from '../../components/Dialog';
import Form from '../../components/Form';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
import Error from '../../components/inputs/Error';
import DeleteDialog from '../../components/DeleteDialog';

const Index = ({ categories, setSidebarOpen }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	const [editingCategory, setEditingCategory] = useState(null);
	const [creatingCategory, setCreatingCategory] = useState(false);
	const [deletingCategory, setDeletingCategory] = useState(null);
	const actions = [
		{
			label: 'Add New',
			icon: 'plus',
			onClick: () => setCreatingCategory(true),
			variant: 'primary',
			can: can.create_song,
		},
	].filter(action => action.can);

	const breadcrumbs = [
		{ name: 'Songs', url: route('songs.index') },
		{ name: 'Song Categories', url: route('song-categories.index') },
	];

	return (
		<>
			<AppHead title="Song Categories" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
					{actions.length > 0 && (
						<PageActionsMenu>
							{actions.map(action => (
								<ActionMenuItem key={action.label} onClick={action.onClick} variant={action.variant}>
									<Icon icon={action.icon} mr />
									{action.label}
								</ActionMenuItem>
							))}
						</PageActionsMenu>
					)}
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="tags" type="solid" className="mr-2" />
						Song Categories
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{actions.map(action => (
						<Button key={action.label} onClick={action.onClick} size="sm" variant={action.variant}>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			{/* Desktop Table */}
			<div className="hidden lg:flex flex-col">
				<SongCategoryTableDesktop
					categories={categories}
					showEditCategory={setEditingCategory}
					showDeleteCategory={setDeletingCategory}
				/>
			</div>

			{/* Mobile Table */}
			<div className="bg-white shadow block lg:hidden">
				<SongCategoryTableMobile
					categories={categories}
					showEditCategory={setEditingCategory}
					showDeleteCategory={setDeletingCategory}
				/>
			</div>

			<EditCategoryDialog isOpen={!!editingCategory} setIsOpen={setEditingCategory} category={editingCategory} />
			<CreateCategoryDialog isOpen={creatingCategory} setIsOpen={setCreatingCategory} />

			<DeleteDialog
				title="Delete Song Category"
				url={deletingCategory ? route('song-categories.destroy', { song_category: deletingCategory }) : '#'}
				isOpen={!!deletingCategory}
				setIsOpen={setDeletingCategory}
			>
				Are you sure you want to delete this song category? Related songs will have this category removed. This
				action cannot be undone.
			</DeleteDialog>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

const EditCategoryDialog = ({ isOpen, setIsOpen, category }) => {
	const { route } = useRoute();

	const { data, setData, post, errors } = useForm({
		_method: 'put',
		title: category?.title ?? '',
	});

	useEffect(() => {
		setData('title', category?.title ?? '');
	}, [category]);

	function submit(e) {
		e.preventDefault();

		post(route('song-categories.update', { song_category: category }), {
			onSuccess: () => setIsOpen(false),
		});
	}

	return (
		<Dialog
			title="Edit Song Category"
			okLabel="Save"
			onOk={submit}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<Form onSubmit={submit}>
				<div className="flex flex-col gap-y-6">
					<div>
						<Label label="Title" forInput="title" />
						<TextInput
							name="title"
							value={data.title}
							updateFn={value => setData('title', value)}
							hasErrors={!!errors['title']}
						/>
						{errors.title && <Error>{errors.title}</Error>}
					</div>
				</div>
			</Form>
		</Dialog>
	);
};

const CreateCategoryDialog = ({ isOpen, setIsOpen }) => {
	const { route } = useRoute();

	const { data, setData, post, errors } = useForm({
		title: '',
	});

	function submit(e) {
		e.preventDefault();

		post(route('song-categories.store'), {
			onSuccess: () => setIsOpen(false),
		});
	}

	return (
		<Dialog
			title="Create Song Category"
			okLabel="Save"
			onOk={submit}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<Form onSubmit={submit}>
				<div className="flex flex-col gap-y-6">
					<div>
						<Label label="Title" forInput="title" />
						<TextInput
							name="title"
							value={data.title}
							updateFn={value => setData('title', value)}
							hasErrors={!!errors['title']}
						/>
						{errors.title && <Error>{errors.title}</Error>}
					</div>
				</div>
			</Form>
		</Dialog>
	);
};
