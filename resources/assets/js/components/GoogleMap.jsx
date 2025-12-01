import React from 'react';
import {usePage} from "@inertiajs/react";

const GoogleMap = ({ placeId }) => {
    const { googleApiKey } = usePage().props;

    return (
        <div style={{ paddingBottom: '56.25%' }} className="relative h-0 overflow-hidden">
            {placeId && (
                <iframe
                    className="w-full h-full absolute top-0 left-0"
                    frameBorder="0"
                    style={{ border: 0 }}
                    src={`https://www.google.com/maps/embed/v1/place?key=${googleApiKey}&q=place_id:${placeId}`}
                    allowFullScreen
                />
            )}
        </div>
    );
}

export default GoogleMap;