import React from 'react';
import PageHeader from "../../../components/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import LearningStatusTag from "../../../components/Song/LearningStatusTag";
import Button from "../../../components/inputs/Button";
import AppHead from "../../../components/AppHead";
import LearningStatus from "../../../LearningStatus";
import useRoute from "../../../hooks/useRoute";
import CollapseGroup from "../../../components/CollapseGroup";
import Icon from "../../../components/Icon";

const Index = ({ song, voiceParts }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Learning Summary - ${song.title}`} />
			<PageHeader
				title="Learning Status Report"
				icon="fa-list-music"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Songs', url: route('songs.index') },
					{ name: song.title, url: route('songs.show', {song}) },
					{ name: 'Learning Status', url: route('songs.singers.index', {song}) },
				]}
			/>

			<nav className="h-full overflow-y-auto" aria-label="Directory">
				<CollapseGroup
					items={voiceParts.map((part) => ({
						title: part.title,
						show: true,
						content: (
							<div key={part.id} className="relative">
								<div className="flex bg-white py-4 border-b border-gray-200">
									{[
										{ slug: 'performance-ready', count: part.members.filter(member => member.learning.status === 'performance-ready').length },
										{ slug: 'assessment-ready', count: part.members.filter(member => member.learning.status === 'assessment-ready').length },
										{ slug: 'not-started', count: part.members.filter(member => member.learning.status === 'not-started').length },
									].map(({slug, count}) => (
										<div className="w-1/3 text-center" key={slug}>
											<LearningStatusTag status={new LearningStatus(slug)} />
											{count}
										</div>
									))}
								</div>
								<ul role="list" className="relative z-0 divide-y divide-gray-200">
									{part.members.map((singer) => (
										<li key={singer.id} className="bg-white">
											<div className="relative px-6 py-5 flex flex-col sm:flex-row items-center space-y-3 sm:space-x-3 hover:bg-gray-50 justify-between items-stretch sm:items-center">
												<div className="flex space-x-2">
													<div className="shrink-0">
														<img className="h-12 w-12 rounded-lg" src={singer.user.avatar_url} alt={singer.user.name}/>
													</div>
													<div className="flex-1 min-w-0">
														<p className="text-sm font-medium text-gray-900">{singer.user.name}</p>
														<p className="text-sm">
															<LearningStatusTag status={new LearningStatus(singer.learning.status)} />
														</p>
													</div>
												</div>
												<div className="shrink-0 flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2 items-stretch">
													{singer.learning.status !== 'performance-ready' &&
														<Button
															href={route('songs.singers.update', {song, singer})}
															method="put"
															data={{ status: 'performance-ready' }}
															size="sm"
														>
															Mark as Performance Ready
														</Button>
													}
													{singer.learning.status !== 'not-started' &&
														<Button
															href={route('songs.singers.update', {song, singer})}
															method="put"
															data={{ status: 'not-started' }}
															size="sm"
														>
															Mark as Learning
														</Button>
													}
												</div>
											</div>
										</li>
									))}
								</ul>
							</div>
						)}
					))} />
			</nav>
		</>
	);
}

Index.layout = page => <TenantLayout children={page} />

export default Index;