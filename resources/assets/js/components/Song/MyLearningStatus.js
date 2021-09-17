import React from 'react';
import Button from "../inputs/Button";

const MyLearningStatus = ({ song }) => (
    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div className="-ml-2 -mt-2 flex flex-wrap items-baseline">
            <h2 className="ml-2 mt-2 text-xl leading-6 font-semibold text-gray-900 mb-4">My Learning Status</h2>
        </div>

        <span className={`mr-4 font-weight-bold text-${song.my_learning.status_colour}`}>
            <i className={`fas fa-fw ${song.my_learning.status_icon} mr-2`} />
            {song.my_learning.status_name}
        </span>
        {song.my_learning.status === 'not-started' && (
            <Button href={route('songs.my-learning.update', song)} method="post" size="sm">I'm Assessment Ready</Button>
        )}
    </div>
);

export default MyLearningStatus;