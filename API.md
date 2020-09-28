# La liste des routes de MonDevise API


### Les routes d'authentification
```
> POST /api/login_check
> POST /api​/register
> PUT /api​/token/refresh
```

### Les routes de l'entité User 
```
> GET /api​/users
> GET /api​/users​/{id}
> PUT /api​/users​/{id}
> DELETE /api​/users​/{id}
```

### Les routes de l'entité Product
```
> GET /api/products
> GET /api/products/{id}
> POST /api/products
> PUT /api/products/{id}
> DELETE /api/products/{id}
```
### Les routes de l'entité Gamme 
```
> POST /api​/gammes
> GET /api​/gammes
> GET /api​/gammes/{id}
> PUT /api/gammes/{id}
> DELETE /api​/gammes/{id}
```

### Les routes de l'entité Component 
```
> POST /api/components
> GET /api/components
> GET /api/components/byType
> GET /api/components/{id}
> PUT /api/components/{id}
> DELETE /api/components/{id}
```

### Les routes de l'entité Panel 
```
> POST /api/panel                         
> PUT /api/panel/{id}                    
> GET /api/panel                         
> GET /api/panel/{id}                    
> DELETE /api/panel/{id}   
```

### Les routes de l'entité Project 
```
> POST /api/projects
> GET /api/projects
> GET /api/projects/groupByClient
> GET /api/projects/{id}
> PUT /api/projects/{id}
> DELETE /api/projects/{id}
```

### Les routes de l'entité SearchProject    
```
> POST /api/projectSearch
> GET /api/salesQuotes
```

### Les routes de l'entité Order    
```
> POST /api/orders
> GET /api/orders  
```

### Les routes de l'entité OrderComponent    
```
> POST /api/orderComponent
> GET /api/orderComponent  
```