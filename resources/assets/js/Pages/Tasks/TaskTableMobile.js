import React from 'react';
import TableMobile, { TableMobileHeader, TableMobileItem} from "../../components/TableMobile";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';

const TaskTableMobile = ({ tasks }) => {
    const { route } = useRoute();

	const bulkEdit = {
		isActiveMobile: false,
		noun: 'Task',
		selectedIds: [],
		totalItems: tasks.length,
	};

    return (
		<div>
			<TableMobileHeader bulkEdit={bulkEdit} />
			<TableMobile pagination={<Pagination details={tasks} />}>
				{tasks.data.map((task) => (
					<TableMobileItem key={task.id} url={route('tasks.show', {task: task.id})}>
						<div className="min-w-0 flex-1 pr-2 lg:grid lg:grid-cols-2 lg:gap-4">
							<div className="flex items-center justify-between">
								<p className="flex items-center min-w-0 mr-1.5">
									<span className="text-sm font-medium text-purple-600 truncate">{task.name}</span>
								</p>
								<div className="text-xs text-gray-500 shrink-0">
									{task.type[0].toUpperCase() + task.type.slice(1)}
								</div>
							</div>
							<div className="flex items-center justify-between">
								<p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
									{task.role.name}
								</p>
							</div>
						</div>
					</TableMobileItem>
				))}
			</TableMobile>
		</div>
    );
}

export default TaskTableMobile;