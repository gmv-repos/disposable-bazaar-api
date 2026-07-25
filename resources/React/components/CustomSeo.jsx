import React, { useEffect, useState } from 'react';
import { Helmet } from 'react-helmet';
import axios from '../Utils/axios';


const CustomSeo = ({ id }) => {
    const [seoData, setSeoData] = useState({
        meta_title: 'Disposable Bazaar',
        meta_description: 'Disposable Bazaar Description',
        canonical_url: '',
        focus_keyword: '',
        robots_txt: '',
        sitemap_xml: '',
        schema: '', // Initially empty schema
    });

    // Fetch SEO data
    // useEffect(() => {
    //     const fetchSeoData = async () => {
    //         try {
    //             const response = await axios.public.get(`user/pages-seo-details/detail/${id}`);
    //             const data = response.data.data;

    //             if (data && data.status) {
    //                 setSeoData(data);
    //             }
    //         } catch (error) {
    //             console.error('Error fetching SEO data:', error);
    //         }
    //     };

    //     fetchSeoData();
    // }, [id]);
    useEffect(() => {
        const fetchSeoData = async () => {
            try {
                const response = await axios.public.get(`page/detail/${id}`);
                const data = response.data.data;

                if (data) {
                    setSeoData(data);
                }
            } catch (error) {
                console.error('Error fetching SEO data:', error);
            }
        };

        fetchSeoData();
    }, [id]);
// console.log(seoData)
    // Assuming schema is in escaped JSON string format
    const schemaData = seoData.schema ? JSON.parse(seoData.schema) : null;
    // console.log('schema', JSON.stringify(schemaData));


    return (
        <Helmet>
            {/* Title */}
            <title>{seoData.meta_title || 'Disposable Bazaar'}</title>

            {/* Meta Description */}
            <meta name="description" content={seoData.meta_description || 'Disposable Bazaar description'} />

            {/* Focus Keyword */}
            {seoData.focus_keyword && <meta name="keywords" content={seoData.focus_keyword} />}

            {/* Canonical URL */}
            {seoData.canonical_url && <link rel="canonical" href={seoData.canonical_url} />}

            {/* Robots.txt rules */}
            {seoData.robots_txt && <meta name="robots" content={seoData.robots_txt} />}

            {/* Schema */}
            {schemaData && (
                <script type="application/ld+json"  >
                    {JSON.stringify(schemaData)}
                </script>
            )}
        </Helmet>
    );
};

export default CustomSeo;
