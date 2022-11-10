
import {Button,Box} from '@mui/material'

import React, { useEffect, useState } from 'react';

import Table from "@material-ui/core/Table";
import TableBody from "@material-ui/core/TableBody";
import TableCell from "@material-ui/core/TableCell";
import TableContainer from "@material-ui/core/TableContainer";
import TableHead from "@material-ui/core/TableHead";
import TableRow from "@material-ui/core/TableRow";
import Paper from "@material-ui/core/Paper";

import axios from "axios";

const App = () => {
  const [data, setData] = useState([]);
  const [order, setOrder] = useState([]);
  useEffect(() => {
    
    if (order != null && order != "")
    axios
      .get("/api/getData/" + order)
      .then((res) => {
        setData(res.data);
        console.log("Result:", data);
      })
      .catch((error) => {
        console.log(error);
      });
  }, [order]);
  return (
    <Box>
      <Button 
        onClick={() => {
          setOrder(1);
        }}
        variant="contained"
        sx={{ margin: 2 }} 
        >
        button1
      </Button>
      <Button 
        onClick={() => {
          setOrder(2);
        }}
        variant="contained"
        sx={{ margin: 2 }} 
        >
        button2
      </Button>
      <Button 
        onClick={() => {
          setOrder(3);
        }}
        variant="contained"
        sx={{ margin: 2 }} >
        button3
      </Button>

      <TableContainer component={Paper}>
        <Table aria-label="simple table" stickyHeader>
          <TableHead>
            <TableRow>
              <TableCell>No.</TableCell>
              <TableCell>Title</TableCell>
              <TableCell align="right">Year</TableCell>
              <TableCell align="right">imdbID</TableCell>
              <TableCell align="right">Type</TableCell>
              <TableCell align="left">Poster</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {data.map((row,num) => (
              <TableRow key={num + 1}>

                <TableCell align="right">{num + 1}</TableCell>
                <TableCell component="th" scope="row">
                  {row.Title}
                </TableCell>
                <TableCell align="right">{row.Year}</TableCell>
                <TableCell align="right">{row.imdbID}</TableCell>
                <TableCell align="right">{row.Type}</TableCell>
                <TableCell align="left">{row.Poster}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  );
};
export default App;

