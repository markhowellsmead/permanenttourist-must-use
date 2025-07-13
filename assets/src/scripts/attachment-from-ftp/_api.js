import { useEffect, useState } from '@wordpress/element';

export const apiStates = {
    IDLE: 'IDLE',
    LOADING: 'LOADING',
    SUCCESS: 'SUCCESS',
    ERROR: 'ERROR',
};

export const apiGet = (apiUrl, nonce, per_page) => {
    const [perPage] = useState(per_page || 24);
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchAllPages = async () => {
            const headers = {
                'Content-Type': 'application/json',
            };
            if (nonce) {
                headers['X-WP-Nonce'] = nonce;
            }

            let page = 1;
            let allData = [];
            let totalPages = 1;

            try {
                do {
                    const pagedUrl = `${apiUrl}&page=${page}`;
                    const response = await fetch(pagedUrl, { headers });
                    const jsonData = await response.json();

                    if (!response.ok) {
                        throw jsonData;
                    }

                    allData = [...allData, ...jsonData];
                    page++;
                } while (page <= totalPages);

                setData(allData);
            } catch (err) {
                setError(err);
            } finally {
                setLoading(false);
            }
        };

        totalPages = parseInt(response.headers.get('X-WP-TotalPages')) || 1;
        fetchAllPages();
    }, [apiUrl, nonce, perPage]);

    return { data, loading, error };
};

export const apiGetAll = (apiUrl, nonce, per_page) => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchAllPages = async () => {
            const headers = {
                'Content-Type': 'application/json',
            };
            if (nonce) {
                headers['X-WP-Nonce'] = nonce;
            }
            let page = 1;
            let allData = [];
            let totalPages = 1;

            try {
                do {
                    const pagedUrl = `${apiUrl}&per_page=100&page=${page}`;
                    const response = await fetch(pagedUrl, { headers });
                    const jsonData = await response.json();
                    const responseHeaders = response.headers;

                    if (responseHeaders.has('X-WP-TotalPages')) {
                        console.log(
                            'X-WP-TotalPages header found',
                            responseHeaders.get('X-WP-TotalPages')
                        );
                    }

                    if (!response.ok) {
                        throw jsonData;
                    }

                    allData = [...allData, ...jsonData];
                    totalPages = parseInt(response.headers.get('X-WP-TotalPages')) || 1;
                    page++;
                    console.log(`Loading page ${page} of ${totalPages}…`);
                } while (page <= totalPages && page <= per_page / 100);

                setData(allData);
            } catch (err) {
                setError(err);
            } finally {
                setLoading(false);
            }
        };

        fetchAllPages();
    }, [apiUrl, nonce]);

    return { data, loading, error };
};
