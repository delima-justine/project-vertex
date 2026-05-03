import 'zone.js/testing';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';

import { Approved } from './approved';

describe('Approved', () => {
  let component: Approved;
  let fixture: ComponentFixture<Approved>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Approved],
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
        provideRouter([]),
      ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Approved);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
