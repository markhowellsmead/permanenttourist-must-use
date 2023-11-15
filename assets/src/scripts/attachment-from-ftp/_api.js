// import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';

const apiGet = (apiUrl, nonce) => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const doRequest = async () => {
            const headers = {
                'Content-Type': 'application/json',
            };

            if (nonce) {
                headers['X-WP-Nonce'] = nonce;
            }

            try {
                const response = await fetch(apiUrl, { headers });
                const jsonData = await response.json();

                if (!response.ok) {
                    setError(jsonData);
                } else {
                    setData(jsonData);
                }
            } catch (error) {
                setError(error);
            } finally {
                setLoading(false);
            }
        };

        doRequest();

        return () => {
            // Cleanup function
            //console.log('Component unmounted! Cleanup performed.');
        };
    }, []);

    return { data, loading, error };
};

export { apiGet };
