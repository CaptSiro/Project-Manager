-- get all parent features
SELECT * 
FROM
	units
    JOIN parents ON parents.ID = units.ID
    	AND units.projectsID = 1


-- get all children features ordered by parentsID (1. features, 2. bugs)
SELECT * 
FROM
	units
    JOIN children ON children.ID = units.ID
    	AND units.projectsID = 1    
    JOIN subunits ON subunits.childrenID = children.ID
ORDER BY subunits.parentsID ASC

-- get completion rate
SELECT
	CAST(done.count AS DECIMAL) / total.count completionRate
FROM (
    SELECT COUNT(units.ID) as "count"
    FROM units
    WHERE units.projectsID = 1
) total, (
	SELECT COUNT(units.ID) as "count"
    FROM units
    WHERE units.projectsID = 1 AND (units.state = 2 OR units.state = 5)
) done

-- get all projects and its completion rate
SELECT
	projects.ID,
  projects.name,
  case 
  	when total.`count` is null then 0
  	else total.`count` 
  end totalUnits,
  case 
  	when finished.`count` is null then 0
  	else finished.`count` 
  end finishedUnits,
  case 
  	when CAST(finished.count AS DECIMAL) / total.count is null then 0
  	else CAST(finished.count AS DECIMAL) / total.count
  end completionRateUnits
FROM
	projects
	LEFT JOIN (
    SELECT COUNT(*) as "count", units.projectsID as "ID"
		FROM units
		GROUP BY 2
  ) total ON total.ID = projects.ID
  LEFT JOIN (
		SELECT COUNT(*) as "count", units.projectsID as "ID"
		FROM units
    WHERE units.state = 2 OR units.state = 5
		GROUP BY 2
	) finished ON finished.ID = projects.ID