alter table public.hanoi_route add column target integer;
alter table public.hanoi_route add column source integer;

select pgr_createTopology('public.hanoi_route', 0.0001, 'geom', 'gid');

CREATE OR REPLACE FUNCTION pgr_fromAtoB(
IN tbl varchar,
IN x1 double precision,
IN y1 double precision,
IN x2 double precision,
IN y2 double precision,
OUT seq integer,
OUT gid integer,
OUT name text,
OUT heading double precision,
OUT cost double precision,
OUT geom geometry
)
RETURNS SETOF record AS
$BODY$
DECLARE
sql text;
rec record;
source integer;
target integer;
point integer;
BEGIN
-- Find nearest node
EXECUTE 'SELECT id::integer FROM hanoi_route_vertices_pgr
ORDER BY the_geom <-> ST_GeometryFromText(''POINT('
|| x1 || ' ' || y1 || ')'',4326) LIMIT 1' INTO rec;
source := rec.id;
EXECUTE 'SELECT id::integer FROM hanoi_route_vertices_pgr
ORDER BY the_geom <-> ST_GeometryFromText(''POINT('
|| x2 || ' ' || y2 || ')'',4326) LIMIT 1' INTO rec;
target := rec.id;
-- Shortest path query
seq := 0;
sql := 'SELECT gid, geom, name, cost, source, target,
ST_Reverse(geom) AS flip_geom FROM ' ||
'pgr_dijkstra(''SELECT gid as id , source::int, target::int, '
|| 'st_length(geom) as cost FROM '
|| quote_ident(tbl) || ''', '
|| source || ', ' || target
|| ' , false), '
|| quote_ident(tbl) || ' WHERE edge = gid ORDER BY seq';
-- Remember start point
point := source;
FOR rec IN EXECUTE sql
LOOP
-- Flip geometry (if required)
IF ( point != rec.source ) THEN
rec.geom := rec.flip_geom;
point := rec.source;
ELSE
point := rec.target;
END IF;
-- Calculate heading (simplified)
EXECUTE 'SELECT degrees( ST_Azimuth(
ST_StartPoint(''' || rec.geom::text || '''),
ST_EndPoint(''' || rec.geom::text || ''') ) )'
INTO heading;
-- Return record
seq := seq + 1;
gid := rec.gid;
name := rec.name;
cost := rec.cost;
geom := rec.geom;
RETURN NEXT;
END LOOP;
RETURN;
END;
$BODY$
LANGUAGE 'plpgsql' VOLATILE STRICT;

SELECT seq, node, edge, cost, geom FROM pgr_dijkstra('SELECT gid as id, source, target, 
st_length(geom) as cost FROM public.hanoi_route', 1, 3000, false) as di
JOIN public.hanoi_route pt
ON di.edge = pt.gid;

SELECT (route.geom) FROM (
SELECT geom FROM pgr_fromAtoB('hanoi_route', 1, 0, 3, 4
) ORDER BY seq) AS route;