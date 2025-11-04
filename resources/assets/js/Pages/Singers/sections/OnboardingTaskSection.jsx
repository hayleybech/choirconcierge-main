import useRoute from '../../../hooks/useRoute';
import CollapsePanel from '../../../components/CollapsePanel';
import Icon from '../../../components/Icon';
import ButtonLink from '../../../components/inputs/ButtonLink';
import React from 'react';

export const OnboardingTaskSection = ({ singer }) => {
	const { route } = useRoute();

	return (
		<CollapsePanel>
			<nav className="flex" aria-label="Progress">
				<ol role="list" className="space-y-6">
					{singer.tasks.map((task, index, tasks) => (
						<li key={index}>
							<span className="flex items-center">
								<span
									className="shrink-0 h-5 w-5 relative flex items-center justify-center"
									aria-hidden="true"
								>
									{(task.pivot.completed && (
										<Icon icon="check-circle" className="text-purple-500 text-sm" />
									)) ||
										((!tasks[index - 1] || tasks[index - 1].pivot.completed) && (
											<>
												<span className="absolute h-4 w-4 rounded-full bg-purple-200" />
												<span className="relative block w-2 h-2 bg-purple-600 rounded-full" />
											</>
										)) || (
											<div className="h-2 w-2 bg-gray-300 rounded-full group-hover:bg-gray-400" />
										)}
								</span>
								{(task.pivot.completed && (
									<span className="ml-3 text-sm font-medium text-gray-500 group-hover:text-gray-900">
										{task.name}
									</span>
								)) ||
									((!tasks[index - 1] || tasks[index - 1].pivot.completed) && (
										<>
											<span className="ml-3 text-sm font-medium text-purple-600">
												{task.name}
											</span>
											{task.can['complete'] && (
												<ButtonLink
													href={route(task.route, { singer: singer.id, task: task.id })}
													size="xs"
													className="ml-3"
												>
													Complete
												</ButtonLink>
											)}
										</>
									)) || (
										<span className="ml-3 text-sm font-medium text-gray-500 group-hover:text-gray-900">
											{task.name}
										</span>
									)}
							</span>
						</li>
					))}
				</ol>
			</nav>
		</CollapsePanel>
	);
};
