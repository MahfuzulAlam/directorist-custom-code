import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom';
import './index.scss';

function ListingCard({ item }) {
	const image = item?.images?.[0]?.src || '';
	const decodeEntities = (str) => {
		if (!str) return '';
		const txt = document.createElement('textarea');
		txt.innerHTML = str;
		return txt.value;
	};
	const title = decodeEntities(item?.name || 'Untitled');
	const tagline = item?.tagline || '';
	const hospital = item?.hospital || 'ABC Hospital';
	return (
		<a className="dcc-card" href={ item.permalink } target="_blank" rel="noopener noreferrer">
			<div className="dcc-card__media" style={ image ? { backgroundImage: `url(${ image })` } : {} }>
				{ item.featured ? <div className="dcc-card__badge">FEATURED</div> : null }
			</div>
			<div className="dcc-card__body">
				<h3 className="dcc-card__title">{ title }</h3>
				<p className="dcc-card__subtitle">{ tagline }</p>
				<div className="dcc-card__meta">
					<span className="dcc-card__hospital">{ hospital }</span>
				</div>
			</div>
		</a>
	);
}

function Grid({ endpoint }) {
	const [data, setData] = useState(null);
	const [error, setError] = useState('');

	useEffect(() => {
		let didCancel = false;
		fetch(endpoint, { credentials: 'omit' })
			.then((res) => {
				if (!res.ok) throw new Error('Network error: ' + res.status);
				return res.json();
			})
			.then((json) => {
				if (!didCancel) setData(Array.isArray(json) ? json : []);
			})
			.catch((err) => {
				if (!didCancel) setError(err.message);
			});
		return () => {
			didCancel = true;
		};
	}, [endpoint]);

	if (error) return <div className="dcc-grid dcc-grid--error">{ 'Failed to load: ' + error }</div>;
	if (!data) return <div className="dcc-grid dcc-grid--loading">Loading featured listings…</div>;
	if (data.length === 0) return <div className="dcc-grid dcc-grid--empty">No featured listings found.</div>;

	return (
		<div className="dcc-grid">
			{ data.map((item) => (
				<ListingCard key={ item.id } item={ item } />
			)) }
		</div>
	);
}

function mountAll() {
	const nodes = document.querySelectorAll('.dcc-remote-listings[data-endpoint]');
	nodes.forEach((node) => {
		const endpoint = node.getAttribute('data-endpoint');
		ReactDOM.render(<Grid endpoint={ endpoint } />, node);
	});
}
if (document.readyState === 'complete' || document.readyState === 'interactive') {
	mountAll();
} else {
	document.addEventListener('DOMContentLoaded', mountAll);
}


