import React from 'react';
import Sorts from "../Sorts";

const SongSorts = ({ }) => (
    <Sorts
        routeName="songs.index"
        options={[
            { id: 'title', name: 'Title', default: true },
            { id: 'created_at', name: 'Dated Created' },
            { id: 'status-title', name: 'Status' },
        ]}
    />
);

export default SongSorts;