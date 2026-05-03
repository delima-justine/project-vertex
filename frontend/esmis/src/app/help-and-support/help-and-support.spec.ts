import 'zone.js/testing';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';

import { HelpAndSupport } from './help-and-support';

describe('HelpAndSupport', () => {
  let component: HelpAndSupport;
  let fixture: ComponentFixture<HelpAndSupport>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HelpAndSupport],
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
        provideRouter([]),
      ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(HelpAndSupport);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
