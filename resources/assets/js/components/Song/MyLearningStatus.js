import React from 'react';
import Button from "../inputs/Button";
import LearningStatusTag from "./LearningStatusTag";
import SectionHeading from "../../SectionHeading";

const MyLearningStatus = ({ song }) => (
    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <SectionHeading>My Learning Status</SectionHeading>

        <LearningStatusTag name={song.my_learning.status_name} colour={song.my_learning.status_colour} icon={song.my_learning.status_icon} />

        {song.my_learning.status === 'not-started' && (
            <Button href={route('songs.my-learning.update', song)} method="post" size="sm">I'm Assessment Ready</Button>
        )}
    </div>
);

export default MyLearningStatus;