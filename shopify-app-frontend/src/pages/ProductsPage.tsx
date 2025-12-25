
import React from 'react';
import {
  Card, IndexTable, useIndexResourceState, TextField, Select,
  Pagination, Layout, Text, Box
} from '@shopify/polaris';
import api from '../api/client';

type Product = {
  id: number | string;
  title: string;
  status: 'ACTIVE'|'DRAFT'|'ARCHIVED';
  vendor?: string;
  image_url?: string;
};

const ProductsPage: React.FC = () => {
  const [items, setItems] = React.useState<Product[]>([]);
  const [page, setPage] = React.useState(1);
  const [pages, setPages] = React.useState(1);
  const [search, setSearch] = React.useState('');
  const [status, setStatus] = React.useState<string>('');

  const pageSize = 10;

  const load = async () => {
    const {data} = await api.get('/products', {
      params: { page, per_page: pageSize, search, status }
    });
    setItems(data.data);
    setPages(data.last_page);
  };
  React.useEffect(() => { load(); }, [page, search, status]);

  // Pass the array of items (not an array of IDs)
  const {selectedResources, allResourcesSelected, handleSelectionChange} =
    useIndexResourceState(items);

  const resourceName = { singular: 'product', plural: 'products' };

  return (
    <Layout>
      <Layout.Section>
        <Card roundedAbove="sm">
          <Box padding="400">
            <Text as="h2" variant="headingMd">Products</Text>
            <Box
              paddingBlockStart="200"
            >
              {/* Polaris v12 TextField: add autoComplete */}
              <TextField
                label="Search by title"
                value={search}
                onChange={setSearch}
                autoComplete="off"
                placeholder="Type a title..."
              />
              <Select
                label="Status"
                options={[
                  {label:'All', value:''},
                  {label:'Active', value:'ACTIVE'},
                  {label:'Draft', value:'DRAFT'},
                  {label:'Archived', value:'ARCHIVED'},
                ]}
                value={status}
                onChange={setStatus}
              />
            </Box>
          </Box>
        </Card>
      </Layout.Section>

      <Layout.Section>
        <Card roundedAbove="sm">
          <IndexTable
            resourceName={resourceName}
            itemCount={items.length}
            selectedItemsCount={allResourcesSelected ? 'All' : selectedResources.length}
            onSelectionChange={handleSelectionChange}
            headings={[
              {title:'Image'},
              {title:'Title'},
              {title:'Status'},
              {title:'Vendor'},
            ]}
          >
            {items.map((item, index) => {
              const id = String(item.id); // IndexTable expects string ids
              return (
                <IndexTable.Row
                  id={id}
                  key={id}
                  position={index}
                  selected={selectedResources.includes(id)}
                >
                  <IndexTable.Cell>
                    {item.image_url
                      ? <img src={item.image_url} alt="" style={{width:40,height:40,objectFit:'cover'}}/>
                      : '—'}
                  </IndexTable.Cell>
                  <IndexTable.Cell>{item.title}</IndexTable.Cell>
                  <IndexTable.Cell>{item.status}</IndexTable.Cell>
                  <IndexTable.Cell>{item.vendor ?? '—'}</IndexTable.Cell>
                </IndexTable.Row>
              );
            })}
          </IndexTable>

          <Box padding="400">
            <Pagination
              hasPrevious={page>1}
              onPrevious={() => setPage(p => Math.max(1, p-1))}
              hasNext={page<pages}
              onNext={() => setPage(p => Math.min(pages, p+1))}
            />
          </Box>
        </Card>
      </Layout.Section>
    </Layout>
  );
};

export default ProductsPage;
